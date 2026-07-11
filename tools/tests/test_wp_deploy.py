from __future__ import annotations

import contextlib
import io
import json
import os
import stat
import sys
import tempfile
import unittest
import urllib.parse
import zipfile
from pathlib import Path
from unittest import mock


TOOLS_DIR = Path(__file__).resolve().parents[1]
REPO_ROOT = TOOLS_DIR.parent
sys.path.insert(0, str(TOOLS_DIR))

import wp_deploy  # noqa: E402


class SitePolicyTests(unittest.TestCase):
    def test_requires_https_for_remote_hosts(self) -> None:
        with self.assertRaises(wp_deploy.DeployError):
            wp_deploy.SitePolicy.from_url("http://example.invalid")

    def test_allows_explicit_local_http_and_rejects_cross_origin(self) -> None:
        policy = wp_deploy.SitePolicy.from_url(
            "http://127.0.0.1:8080/wordpress",
            allow_http_localhost=True,
        )
        self.assertEqual(
            policy.endpoint("wp-admin/"),
            "http://127.0.0.1:8080/wordpress/wp-admin/",
        )
        with self.assertRaises(wp_deploy.DeployError):
            policy.validate_url("http://attacker.invalid/wordpress/wp-admin/")
        with self.assertRaises(wp_deploy.DeployError):
            policy.validate_url("http://127.0.0.1:8080/outside/wp-admin/")

    def test_rejects_userinfo_query_and_path_traversal(self) -> None:
        for value in (
            "https://user@example.invalid",
            "https://example.invalid/?token=x",
            "https://example.invalid/%2e%2e/admin",
        ):
            with self.subTest(value=value), self.assertRaises(wp_deploy.DeployError):
                wp_deploy.SitePolicy.from_url(value)


class HtmlParsingTests(unittest.TestCase):
    def setUp(self) -> None:
        self.policy = wp_deploy.SitePolicy.from_url("https://example.invalid")

    def response(self, body: str, path: str) -> wp_deploy.HttpResponse:
        return wp_deploy.HttpResponse(
            200,
            "https://example.invalid" + path,
            {"content-type": "text/html; charset=UTF-8"},
            body.encode(),
        )

    def test_theme_editor_parser_extracts_only_editor_content(self) -> None:
        response = self.response(
            """
            <form id="template" action="theme-editor.php" method="post">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="file" value="front-page.php">
              <input type="hidden" name="theme" value="interagents-theme">
              <input type="hidden" name="nonce" value="test-nonce">
              <textarea name="newcontent">&lt;?php echo &quot;safe&quot;;</textarea>
            </form>
            """,
            "/wp-admin/theme-editor.php?file=front-page.php&amp;theme=interagents-theme",
        )
        snapshot = wp_deploy.parse_theme_editor(
            self.policy,
            response,
            theme_slug="interagents-theme",
            remote_path="front-page.php",
        )
        self.assertEqual(snapshot.content, '<?php echo "safe";')
        self.assertEqual(snapshot.nonce, "test-nonce")
        self.assertTrue(snapshot.action_url.endswith("/wp-admin/theme-editor.php"))

    def test_theme_editor_parser_rejects_wrong_identity(self) -> None:
        response = self.response(
            """
            <form action="theme-editor.php" method="post">
              <input name="file" value="other.php">
              <input name="theme" value="interagents-theme">
              <input name="nonce" value="n">
              <textarea name="newcontent">x</textarea>
            </form>
            """,
            "/wp-admin/theme-editor.php",
        )
        with self.assertRaises(wp_deploy.DeployError):
            wp_deploy.parse_theme_editor(
                self.policy,
                response,
                theme_slug="interagents-theme",
                remote_path="front-page.php",
            )

    def test_plugin_upload_and_overwrite_forms_are_guarded(self) -> None:
        upload = self.response(
            """
            <form action="update.php?action=upload-plugin" method="post" enctype="multipart/form-data">
              <input type="hidden" name="_wpnonce" value="upload-nonce">
              <input type="file" name="pluginzip">
            </form>
            """,
            "/wp-admin/plugin-install.php?tab=upload",
        )
        form, action = wp_deploy.parse_plugin_upload_form(self.policy, upload)
        self.assertEqual(form.fields["_wpnonce"], "upload-nonce")
        self.assertEqual(urllib.parse.parse_qs(urllib.parse.urlsplit(action).query)["action"], ["upload-plugin"])

        overwrite = self.response(
            """
            <a class="update-from-upload-overwrite"
               href="update.php?action=upload-plugin&amp;overwrite=update-plugin&amp;_wpnonce=replace-nonce">
               Replace current with uploaded
            </a>
            """,
            "/wp-admin/update.php?action=upload-plugin",
        )
        match = wp_deploy.find_plugin_overwrite_action(self.policy, overwrite)
        self.assertIsNotNone(match)
        self.assertIsNone(match[0])
        query = urllib.parse.parse_qs(urllib.parse.urlsplit(match[1]).query)
        self.assertEqual(query["overwrite"], ["update-plugin"])
        self.assertEqual(query["_wpnonce"], ["replace-nonce"])

    def test_plugin_rows_capture_activation_link_without_page_body_logging(self) -> None:
        parser = wp_deploy.parse_html(
            """
            <table><tr class="inactive" data-plugin="sample/sample.php">
              <td><a href="plugins.php?action=activate&amp;plugin=sample%2Fsample.php&amp;_wpnonce=n">Activate</a></td>
            </tr></table>
            """
        )
        self.assertEqual(len(parser.plugin_rows), 1)
        row = parser.plugin_rows[0]
        self.assertFalse(row.active)
        self.assertIn("action=activate", row.links[0])

    def test_speedycache_discovery_ignores_external_links(self) -> None:
        response = self.response(
            """
            <a href="https://news.example.invalid/item">News</a>
            <a href="admin-post.php?action=speedycache_delete_cache&amp;_wpnonce=n">Purge</a>
            """,
            "/wp-admin/",
        )
        link = wp_deploy.find_speedycache_purge_link(self.policy, response)
        self.assertEqual(
            urllib.parse.parse_qs(urllib.parse.urlsplit(link).query)["action"],
            ["speedycache_delete_cache"],
        )

    def test_speedycache_discovery_keeps_cache_and_minified_actions(self) -> None:
        response = self.response(
            """
            <a href="admin-post.php?action=speedycache_delete_cache&amp;_wpnonce=a">Purge cache</a>
            <a href="admin-post.php?action=speedycache_delete_cache&amp;minified=1&amp;_wpnonce=b">Purge minified</a>
            """,
            "/wp-admin/",
        )
        links = wp_deploy.find_speedycache_purge_links(self.policy, response)
        self.assertEqual(len(links), 2)
        self.assertTrue(any("minified=1" in link for link in links))


class LocalValidationTests(unittest.TestCase):
    def test_credentials_file_requires_restrictive_permissions_and_repr_is_hidden(self) -> None:
        with tempfile.TemporaryDirectory() as directory:
            path = Path(directory) / "credentials.json"
            secret = "not-a-real-secret"
            path.write_text(
                json.dumps(
                    {
                        "site_url": "https://example.invalid",
                        "username": "operator",
                        "password": secret,
                    }
                )
            )
            path.chmod(0o600)
            credentials = wp_deploy.load_credentials(path, allow_http_localhost=False)
            self.assertNotIn(secret, repr(credentials))
            if os.name == "posix":
                path.chmod(0o644)
                with self.assertRaises(wp_deploy.DeployError):
                    wp_deploy.load_credentials(path, allow_http_localhost=False)

    def test_manifest_is_an_exact_allowlist_and_rejects_traversal(self) -> None:
        with tempfile.TemporaryDirectory() as directory:
            root = Path(directory)
            theme = root / "theme"
            theme.mkdir()
            (theme / "style.css").write_text("/* safe */\n")
            manifest = root / "manifest.json"
            manifest.write_text(
                json.dumps(
                    {
                        "schema": 1,
                        "theme": {"slug": "sample-theme", "root": "theme", "files": ["style.css"]},
                    }
                )
            )
            plan = wp_deploy.load_theme_plan(manifest, root)
            self.assertEqual([item.remote_path for item in plan.files], ["style.css"])
            bad = root / "bad.json"
            bad.write_text(
                json.dumps(
                    {
                        "schema": 1,
                        "theme": {"slug": "sample-theme", "root": "theme", "files": ["../secret"]},
                    }
                )
            )
            with self.assertRaises(wp_deploy.DeployError):
                wp_deploy.load_theme_plan(bad, root)

    def test_plugin_zip_validation_accepts_one_slug_and_rejects_traversal(self) -> None:
        with tempfile.TemporaryDirectory() as directory:
            root = Path(directory)
            good = root / "plugin.zip"
            with zipfile.ZipFile(good, "w") as archive:
                archive.writestr(
                    "sample/sample.php",
                    "<?php\n/*\nPlugin Name: Sample\n*/\n",
                )
            plan = wp_deploy.validate_plugin_zip(
                good,
                slug="sample",
                main_file="sample/sample.php",
                allow_replace=False,
            )
            self.assertEqual(plan.main_file, "sample/sample.php")

            bad = root / "bad.zip"
            with zipfile.ZipFile(bad, "w") as archive:
                archive.writestr("../escape.php", "bad")
            with self.assertRaises(wp_deploy.DeployError):
                wp_deploy.validate_plugin_zip(
                    bad,
                    slug="sample",
                    main_file="sample/sample.php",
                    allow_replace=False,
                )

    def test_atomic_evidence_redacts_secret_and_uses_private_mode(self) -> None:
        with tempfile.TemporaryDirectory() as directory:
            secret = "not-a-real-secret"
            recorder = wp_deploy.EvidenceRecorder(
                Path(directory),
                mode="check_login",
                dry_run=True,
                site_origin_hash="0" * 64,
                secrets_to_redact=(secret,),
            )
            recorder.event("sample", "planned", message="password=" + secret)
            recorder.finish("dry_run")
            evidence_path = recorder.run_dir / "evidence.json"
            content = evidence_path.read_text()
            self.assertNotIn(secret, content)
            self.assertIn("[REDACTED]", content)
            if os.name == "posix":
                self.assertEqual(stat.S_IMODE(evidence_path.stat().st_mode), 0o600)


class MockedWorkflowTests(unittest.TestCase):
    def test_wxr_download_uses_mock_client_and_writes_only_xml(self) -> None:
        policy = wp_deploy.SitePolicy.from_url("https://example.invalid")
        page = wp_deploy.HttpResponse(
            200,
            "https://example.invalid/wp-admin/export.php",
            {"content-type": "text/html"},
            b'<form action="export.php" method="get"><input name="download" value="true"></form>',
        )
        xml = b'<?xml version="1.0"?><rss xmlns:wp="http://wordpress.org/export/1.2/"></rss>'
        download = wp_deploy.HttpResponse(
            200,
            "https://example.invalid/wp-admin/export.php?download=true&content=all",
            {"content-type": "application/xml"},
            xml,
        )
        fake_client = mock.Mock()
        fake_client.policy = policy
        fake_client.request.side_effect = [page, download]
        with tempfile.TemporaryDirectory() as directory:
            recorder = wp_deploy.EvidenceRecorder(
                Path(directory),
                mode="backup_only",
                dry_run=False,
                site_origin_hash="1" * 64,
            )
            self.assertTrue(wp_deploy.attempt_wxr_backup(fake_client, recorder))
            self.assertEqual((recorder.run_dir / "backup" / "wordpress-export.xml").read_bytes(), xml)
        self.assertEqual(fake_client.request.call_count, 2)

    def test_cli_dry_run_never_builds_http_client(self) -> None:
        with tempfile.TemporaryDirectory() as directory:
            root = Path(directory)
            manifest = root / "manifest.json"
            manifest.write_text(
                json.dumps(
                    {
                        "schema": 1,
                        "theme": {
                            "slug": "interagents-theme",
                            "root": "interagents-theme",
                            "files": ["style.css"],
                        },
                    }
                )
            )
            stdout = io.StringIO()
            stderr = io.StringIO()
            with mock.patch.object(wp_deploy, "HttpClient", side_effect=AssertionError("network client built")):
                with contextlib.redirect_stdout(stdout), contextlib.redirect_stderr(stderr):
                    result = wp_deploy.main(
                        [
                            "--backup-only",
                            "--dry-run",
                            "--site-url",
                            "https://example.invalid",
                            "--manifest",
                            str(manifest),
                            "--evidence-dir",
                            str(root / "evidence"),
                        ]
                    )
            self.assertEqual(result, 0, stderr.getvalue())
            self.assertIn("DRY RUN OK", stdout.getvalue())
            self.assertEqual(stderr.getvalue(), "")


if __name__ == "__main__":
    unittest.main()
