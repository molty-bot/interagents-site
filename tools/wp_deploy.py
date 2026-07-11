#!/usr/bin/env python3
"""Fail-closed WordPress deployment helper for the Interagents custom theme.

The implementation intentionally uses only the Python standard library.  It
never persists cookies, never logs response bodies, and records only an
allow-listed, redacted evidence trail.  A dry run performs no HTTP requests.
"""

from __future__ import annotations

import argparse
import hashlib
import http.cookiejar
import json
import os
import re
import secrets
import ssl
import stat
import sys
import tempfile
import time
import urllib.error
import urllib.parse
import urllib.request
import zipfile
from dataclasses import dataclass, field
from datetime import datetime, timezone
from html.parser import HTMLParser
from pathlib import Path, PurePosixPath
from typing import Any, Iterable, Mapping, Sequence


MAX_HTML_BYTES = 8 * 1024 * 1024
MAX_THEME_FILE_BYTES = 5 * 1024 * 1024
MAX_PLUGIN_ZIP_BYTES = 50 * 1024 * 1024
MAX_PLUGIN_UNPACKED_BYTES = 150 * 1024 * 1024
MAX_PLUGIN_FILES = 5000
MAX_WXR_BYTES = 100 * 1024 * 1024
MAX_JSON_BYTES = 128 * 1024
USER_AGENT = "InteragentsSafeDeployer/1.0"
ENV_SITE_URL = "WP_DEPLOY_SITE_URL"
ENV_USERNAME = "WP_DEPLOY_USERNAME"
ENV_PASSWORD = "WP_DEPLOY_PASSWORD"


class DeployError(RuntimeError):
    """An expected failure whose message is safe to show to an operator."""

    def __init__(self, code: str, safe_message: str):
        super().__init__(safe_message)
        self.code = code
        self.safe_message = safe_message


def utc_now() -> str:
    return datetime.now(timezone.utc).replace(microsecond=0).isoformat()


def sha256_bytes(data: bytes) -> str:
    return hashlib.sha256(data).hexdigest()


def sha256_file(path: Path) -> str:
    digest = hashlib.sha256()
    with path.open("rb") as handle:
        while True:
            block = handle.read(1024 * 1024)
            if not block:
                break
            digest.update(block)
    return digest.hexdigest()


def text_sha256(value: str) -> str:
    normalized = value.replace("\r\n", "\n").replace("\r", "\n")
    return sha256_bytes(normalized.encode("utf-8"))


def atomic_write(path: Path, data: bytes, mode: int = 0o600) -> None:
    path.parent.mkdir(parents=True, exist_ok=True, mode=0o700)
    try:
        os.chmod(path.parent, 0o700)
    except OSError:
        pass
    fd, temporary_name = tempfile.mkstemp(prefix=".tmp-", dir=str(path.parent))
    temporary_path = Path(temporary_name)
    try:
        os.fchmod(fd, mode)
        with os.fdopen(fd, "wb") as handle:
            handle.write(data)
            handle.flush()
            os.fsync(handle.fileno())
        os.replace(temporary_path, path)
        try:
            os.chmod(path, mode)
        except OSError:
            pass
    except Exception:
        try:
            os.close(fd)
        except OSError:
            pass
        temporary_path.unlink(missing_ok=True)
        raise


def read_limited_file(path: Path, maximum: int, error_code: str) -> bytes:
    if path.is_symlink() or not path.is_file():
        raise DeployError(error_code, "Required local file is missing or unsafe.")
    size = path.stat().st_size
    if size <= 0 or size > maximum:
        raise DeployError(error_code, "Required local file has an unsafe size.")
    with path.open("rb") as handle:
        data = handle.read(maximum + 1)
    if len(data) > maximum:
        raise DeployError(error_code, "Required local file exceeds the size limit.")
    return data


def validate_slug(value: str, label: str) -> str:
    if not re.fullmatch(r"[A-Za-z0-9_-]{1,100}", value or ""):
        raise DeployError("invalid_slug", f"{label} is not a safe WordPress slug.")
    return value


def validate_relative_path(value: str, label: str) -> str:
    if not isinstance(value, str) or not value or len(value) > 300:
        raise DeployError("invalid_path", f"{label} is not a safe relative path.")
    if "\\" in value or "\x00" in value or value.startswith("/"):
        raise DeployError("invalid_path", f"{label} is not a safe relative path.")
    path = PurePosixPath(value)
    if any(part in {"", ".", ".."} for part in path.parts):
        raise DeployError("invalid_path", f"{label} is not a safe relative path.")
    return path.as_posix()


def ensure_within(root: Path, candidate: Path, label: str) -> Path:
    resolved_root = root.resolve(strict=True)
    resolved_candidate = candidate.resolve(strict=True)
    try:
        resolved_candidate.relative_to(resolved_root)
    except ValueError as exc:
        raise DeployError("unsafe_local_path", f"{label} escapes the repository root.") from exc
    return resolved_candidate


@dataclass(frozen=True, repr=False)
class Credentials:
    site_url: str
    username: str
    password: str


def _load_json(path: Path, maximum: int, code: str) -> Mapping[str, Any]:
    raw = read_limited_file(path, maximum, code)
    try:
        parsed = json.loads(raw.decode("utf-8"))
    except (UnicodeDecodeError, json.JSONDecodeError) as exc:
        raise DeployError(code, "JSON input is invalid.") from exc
    if not isinstance(parsed, dict):
        raise DeployError(code, "JSON input must contain an object.")
    return parsed


def load_credentials(path: Path | None, *, allow_http_localhost: bool) -> Credentials:
    env_values = {
        "site_url": os.environ.get(ENV_SITE_URL),
        "username": os.environ.get(ENV_USERNAME),
        "password": os.environ.get(ENV_PASSWORD),
    }
    if path is not None and any(value is not None for value in env_values.values()):
        raise DeployError(
            "ambiguous_credentials",
            "Use either a credentials file or deployment environment variables, not both.",
        )

    if path is not None:
        if path.is_symlink() or not path.is_file():
            raise DeployError("credentials_file", "Credentials file is missing or unsafe.")
        if os.name == "posix" and stat.S_IMODE(path.stat().st_mode) & 0o077:
            raise DeployError("credentials_permissions", "Credentials file permissions must be 0600 or stricter.")
        data = _load_json(path, MAX_JSON_BYTES, "credentials_file")
        if set(data) != {"site_url", "username", "password"}:
            raise DeployError("credentials_schema", "Credentials file has unexpected or missing keys.")
        values = {key: data[key] for key in ("site_url", "username", "password")}
    else:
        if not all(env_values.values()):
            raise DeployError(
                "credentials_missing",
                f"Set {ENV_SITE_URL}, {ENV_USERNAME}, and {ENV_PASSWORD}, or pass --credentials-file.",
            )
        values = env_values

    if not all(isinstance(values[key], str) and 0 < len(values[key]) <= 1024 for key in values):
        raise DeployError("credentials_schema", "Credential values are invalid.")
    if any("\x00" in values[key] for key in values):
        raise DeployError("credentials_schema", "Credential values are invalid.")
    policy = SitePolicy.from_url(values["site_url"], allow_http_localhost=allow_http_localhost)
    return Credentials(policy.base_url, values["username"], values["password"])


@dataclass(frozen=True)
class SitePolicy:
    base_url: str
    scheme: str
    hostname: str
    port: int
    base_path: str

    @classmethod
    def from_url(cls, value: str, *, allow_http_localhost: bool = False) -> "SitePolicy":
        if not isinstance(value, str) or not value or len(value) > 2048:
            raise DeployError("invalid_site_url", "Site URL is invalid.")
        parsed = urllib.parse.urlsplit(value)
        if parsed.username or parsed.password or parsed.query or parsed.fragment:
            raise DeployError("invalid_site_url", "Site URL must not contain credentials, a query, or a fragment.")
        hostname = (parsed.hostname or "").lower()
        if not hostname:
            raise DeployError("invalid_site_url", "Site URL has no hostname.")
        if parsed.scheme == "https":
            port = parsed.port or 443
        elif parsed.scheme == "http" and allow_http_localhost and hostname in {"localhost", "127.0.0.1", "::1"}:
            port = parsed.port or 80
        else:
            raise DeployError("invalid_site_url", "Site URL must use HTTPS.")
        decoded_path = urllib.parse.unquote(parsed.path or "")
        if "\\" in decoded_path or any(part == ".." for part in PurePosixPath(decoded_path).parts):
            raise DeployError("invalid_site_url", "Site URL path is unsafe.")
        base_path = "/" + decoded_path.strip("/") if decoded_path.strip("/") else ""
        netloc = f"[{hostname}]" if ":" in hostname else hostname
        if (parsed.scheme, port) not in {("https", 443), ("http", 80)}:
            netloc = f"{netloc}:{port}"
        base_url = urllib.parse.urlunsplit((parsed.scheme, netloc, base_path, "", ""))
        return cls(base_url, parsed.scheme, hostname, port, base_path)

    def validate_url(self, value: str) -> str:
        parsed = urllib.parse.urlsplit(value)
        hostname = (parsed.hostname or "").lower()
        if parsed.scheme != self.scheme or hostname != self.hostname:
            raise DeployError("cross_origin", "A WordPress response attempted to leave the configured origin.")
        port = parsed.port or (443 if parsed.scheme == "https" else 80)
        if port != self.port or parsed.username or parsed.password:
            raise DeployError("cross_origin", "A WordPress response attempted to leave the configured origin.")
        decoded_path = urllib.parse.unquote(parsed.path or "/")
        if "\\" in decoded_path or any(part == ".." for part in PurePosixPath(decoded_path).parts):
            raise DeployError("unsafe_remote_path", "A WordPress response contained an unsafe path.")
        if self.base_path and not (decoded_path == self.base_path or decoded_path.startswith(self.base_path + "/")):
            raise DeployError("cross_origin", "A WordPress response left the configured WordPress path.")
        return value

    def resolve(self, reference: str, *, current_url: str | None = None) -> str:
        base = current_url or (self.base_url.rstrip("/") + "/")
        target = urllib.parse.urljoin(base, reference)
        return self.validate_url(target)

    def endpoint(self, relative: str) -> str:
        if relative.startswith("/"):
            raise DeployError("unsafe_remote_path", "Internal endpoint must be relative.")
        return self.resolve(relative, current_url=self.base_url.rstrip("/") + "/")


class SameOriginRedirectHandler(urllib.request.HTTPRedirectHandler):
    def __init__(self, policy: SitePolicy):
        super().__init__()
        self.policy = policy

    def redirect_request(self, req, fp, code, msg, headers, newurl):  # type: ignore[override]
        target = self.policy.resolve(newurl, current_url=req.full_url)
        return super().redirect_request(req, fp, code, msg, headers, target)


@dataclass(frozen=True, repr=False)
class HttpResponse:
    status: int
    final_url: str
    headers: Mapping[str, str]
    body: bytes

    def text(self) -> str:
        content_type = self.headers.get("content-type", "")
        match = re.search(r"charset=([A-Za-z0-9._-]+)", content_type, re.I)
        encoding = match.group(1) if match else "utf-8"
        try:
            return self.body.decode(encoding, errors="replace")
        except LookupError:
            return self.body.decode("utf-8", errors="replace")


def _read_response(response, maximum: int) -> bytes:
    data = response.read(maximum + 1)
    if len(data) > maximum:
        raise DeployError("response_too_large", "A WordPress response exceeded the safety limit.")
    return data


class HttpClient:
    def __init__(self, policy: SitePolicy, *, timeout: float, ca_file: Path | None = None):
        self.policy = policy
        self.timeout = timeout
        self.cookies = http.cookiejar.CookieJar()
        context = ssl.create_default_context(cafile=str(ca_file) if ca_file else None)
        self.opener = urllib.request.build_opener(
            urllib.request.ProxyHandler({}),
            urllib.request.HTTPCookieProcessor(self.cookies),
            urllib.request.HTTPSHandler(context=context),
            SameOriginRedirectHandler(policy),
        )

    def request(
        self,
        label: str,
        url: str,
        *,
        method: str = "GET",
        data: bytes | None = None,
        headers: Mapping[str, str] | None = None,
        maximum: int = MAX_HTML_BYTES,
        allow_status: Iterable[int] = (),
    ) -> HttpResponse:
        target = self.policy.validate_url(url)
        request_headers = {
            "User-Agent": USER_AGENT,
            "Accept-Encoding": "identity",
            "Cache-Control": "no-cache",
        }
        if headers:
            request_headers.update(headers)
        request = urllib.request.Request(target, data=data, headers=request_headers, method=method)
        allowed = set(allow_status)
        try:
            with self.opener.open(request, timeout=self.timeout) as response:
                final_url = self.policy.validate_url(response.geturl())
                body = _read_response(response, maximum)
                return HttpResponse(
                    int(response.status),
                    final_url,
                    {key.lower(): value for key, value in response.headers.items()},
                    body,
                )
        except urllib.error.HTTPError as exc:
            if exc.code in allowed:
                try:
                    body = _read_response(exc, maximum)
                finally:
                    exc.close()
                final_url = self.policy.validate_url(exc.geturl())
                return HttpResponse(
                    int(exc.code),
                    final_url,
                    {key.lower(): value for key, value in exc.headers.items()},
                    body,
                )
            exc.close()
            raise DeployError("http_status", f"{label} returned HTTP {exc.code}.") from None
        except (urllib.error.URLError, TimeoutError, OSError):
            raise DeployError("network_error", f"{label} could not be completed securely.") from None


@dataclass(repr=False)
class ParsedForm:
    action: str = ""
    method: str = "get"
    form_id: str = ""
    fields: dict[str, str] = field(default_factory=dict)
    field_types: dict[str, str] = field(default_factory=dict)
    textareas: dict[str, str] = field(default_factory=dict)
    file_inputs: set[str] = field(default_factory=set)


@dataclass
class PluginRow:
    plugin_file: str
    classes: set[str]
    links: list[str]

    @property
    def active(self) -> bool:
        return "active" in self.classes


class WordPressHTMLParser(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.forms: list[ParsedForm] = []
        self.links: list[str] = []
        self.body_classes: set[str] = set()
        self.ids: set[str] = set()
        self._form: ParsedForm | None = None
        self._textarea_name: str | None = None
        self._textarea_parts: list[str] = []
        self._plugin_row: PluginRow | None = None
        self.plugin_rows: list[PluginRow] = []

    def handle_starttag(self, tag: str, attrs: Sequence[tuple[str, str | None]]) -> None:
        values = {key: value or "" for key, value in attrs}
        if values.get("id"):
            self.ids.add(values["id"])
        if tag == "body":
            self.body_classes.update(values.get("class", "").split())
        if tag == "form":
            if self._form is not None:
                raise DeployError("html_parse", "WordPress returned malformed nested forms.")
            self._form = ParsedForm(
                action=values.get("action", ""),
                method=values.get("method", "get").lower(),
                form_id=values.get("id", ""),
            )
        elif tag == "input" and self._form is not None:
            name = values.get("name", "")
            input_type = values.get("type", "text").lower()
            if not name:
                return
            if input_type == "file":
                self._form.file_inputs.add(name)
                self._form.field_types[name] = input_type
                return
            if input_type in {"checkbox", "radio"} and "checked" not in values:
                return
            self._form.fields[name] = values.get("value", "")
            self._form.field_types[name] = input_type
        elif tag == "textarea" and self._form is not None:
            name = values.get("name", "")
            if name:
                self._textarea_name = name
                self._textarea_parts = []
        elif tag == "a" and values.get("href"):
            self.links.append(values["href"])
            if self._plugin_row is not None:
                self._plugin_row.links.append(values["href"])
        elif tag == "tr" and values.get("data-plugin"):
            self._plugin_row = PluginRow(
                plugin_file=values["data-plugin"],
                classes=set(values.get("class", "").split()),
                links=[],
            )

    def handle_endtag(self, tag: str) -> None:
        if tag == "textarea" and self._form is not None and self._textarea_name is not None:
            self._form.textareas[self._textarea_name] = "".join(self._textarea_parts)
            self._textarea_name = None
            self._textarea_parts = []
        elif tag == "form" and self._form is not None:
            self.forms.append(self._form)
            self._form = None
        elif tag == "tr" and self._plugin_row is not None:
            self.plugin_rows.append(self._plugin_row)
            self._plugin_row = None

    def handle_data(self, data: str) -> None:
        if self._textarea_name is not None:
            self._textarea_parts.append(data)


def parse_html(value: str) -> WordPressHTMLParser:
    parser = WordPressHTMLParser()
    try:
        parser.feed(value)
        parser.close()
    except DeployError:
        raise
    except Exception as exc:
        raise DeployError("html_parse", "WordPress HTML could not be parsed safely.") from exc
    return parser


def form_action(policy: SitePolicy, response_url: str, form: ParsedForm, expected_suffix: str) -> str:
    target = policy.resolve(form.action or response_url, current_url=response_url)
    if not urllib.parse.urlsplit(target).path.endswith(expected_suffix):
        raise DeployError("unexpected_form_action", "WordPress returned an unexpected form action.")
    return target


def encode_form(fields: Mapping[str, str]) -> bytes:
    return urllib.parse.urlencode(fields).encode("utf-8")


def encode_multipart(
    fields: Mapping[str, str],
    *,
    file_field: str,
    filename: str,
    file_data: bytes,
    content_type: str,
) -> tuple[bytes, str]:
    boundary = "----Interagents" + secrets.token_hex(16)
    chunks: list[bytes] = []
    for name, value in fields.items():
        if not re.fullmatch(r"[A-Za-z0-9_.-]{1,100}", name):
            raise DeployError("unsafe_form_field", "WordPress returned an unsafe upload field.")
        chunks.extend(
            [
                f"--{boundary}\r\n".encode(),
                f'Content-Disposition: form-data; name="{name}"\r\n\r\n'.encode(),
                value.encode("utf-8"),
                b"\r\n",
            ]
        )
    safe_filename = Path(filename).name.replace('"', "")
    chunks.extend(
        [
            f"--{boundary}\r\n".encode(),
            f'Content-Disposition: form-data; name="{file_field}"; filename="{safe_filename}"\r\n'.encode(),
            f"Content-Type: {content_type}\r\n\r\n".encode(),
            file_data,
            b"\r\n",
            f"--{boundary}--\r\n".encode(),
        ]
    )
    return b"".join(chunks), f"multipart/form-data; boundary={boundary}"


@dataclass(frozen=True, repr=False)
class SourceFile:
    remote_path: str
    local_path: Path
    content: bytes
    sha256: str

    def text(self) -> str:
        try:
            return self.content.decode("utf-8")
        except UnicodeDecodeError as exc:
            raise DeployError("theme_encoding", "Theme source files must be UTF-8.") from exc


@dataclass(frozen=True, repr=False)
class ThemePlan:
    slug: str
    root: Path
    files: tuple[SourceFile, ...]


@dataclass(frozen=True, repr=False)
class PluginPlan:
    zip_path: Path
    slug: str
    main_file: str
    zip_sha256: str
    main_text: str
    main_sha256: str
    allow_replace: bool


def load_theme_plan(manifest_path: Path, repo_root: Path) -> ThemePlan:
    data = _load_json(manifest_path, MAX_JSON_BYTES, "manifest")
    if set(data) != {"schema", "theme"} or data.get("schema") != 1 or not isinstance(data.get("theme"), dict):
        raise DeployError("manifest_schema", "Deployment manifest has an unsupported schema.")
    theme = data["theme"]
    if set(theme) != {"slug", "root", "files"}:
        raise DeployError("manifest_schema", "Deployment manifest has unexpected or missing theme keys.")
    slug = validate_slug(theme["slug"], "Theme slug")
    root_rel = validate_relative_path(theme["root"], "Theme root")
    if not isinstance(theme["files"], list) or not theme["files"]:
        raise DeployError("manifest_schema", "Deployment manifest must list changed theme files.")
    if len(theme["files"]) > 100:
        raise DeployError("manifest_schema", "Deployment manifest lists too many theme files.")
    root = ensure_within(repo_root, repo_root / root_rel, "Theme root")
    if root.is_symlink() or not root.is_dir():
        raise DeployError("unsafe_local_path", "Theme root is missing or unsafe.")

    seen: set[str] = set()
    source_files: list[SourceFile] = []
    for raw in theme["files"]:
        remote_path = validate_relative_path(raw, "Theme file")
        if remote_path in seen:
            raise DeployError("manifest_schema", "Deployment manifest contains duplicate theme files.")
        seen.add(remote_path)
        local = ensure_within(root, root / remote_path, "Theme file")
        content = read_limited_file(local, MAX_THEME_FILE_BYTES, "theme_file")
        source_files.append(SourceFile(remote_path, local, content, sha256_bytes(content)))
    return ThemePlan(slug, root, tuple(source_files))


def validate_plugin_zip(
    zip_path: Path,
    *,
    slug: str,
    main_file: str,
    allow_replace: bool,
) -> PluginPlan:
    slug = validate_slug(slug, "Plugin slug")
    main_file = validate_relative_path(main_file, "Plugin main file")
    if not main_file.startswith(slug + "/"):
        raise DeployError("plugin_layout", "Plugin main file must be inside the plugin slug directory.")
    raw_zip = read_limited_file(zip_path, MAX_PLUGIN_ZIP_BYTES, "plugin_zip")
    try:
        with zipfile.ZipFile(zip_path, "r") as archive:
            infos = archive.infolist()
            if not infos or len(infos) > MAX_PLUGIN_FILES:
                raise DeployError("plugin_zip", "Plugin ZIP has an unsafe file count.")
            unpacked = 0
            names: set[str] = set()
            for info in infos:
                name = validate_relative_path(info.filename.rstrip("/"), "Plugin ZIP entry")
                if name in names:
                    raise DeployError("plugin_zip", "Plugin ZIP contains duplicate paths.")
                if not name.startswith(slug + "/") and name != slug:
                    raise DeployError("plugin_layout", "Plugin ZIP must contain exactly one plugin slug directory.")
                unix_mode = (info.external_attr >> 16) & 0xFFFF
                if stat.S_ISLNK(unix_mode):
                    raise DeployError("plugin_zip", "Plugin ZIP must not contain symbolic links.")
                unpacked += info.file_size
                if unpacked > MAX_PLUGIN_UNPACKED_BYTES:
                    raise DeployError("plugin_zip", "Plugin ZIP expands beyond the safety limit.")
                names.add(info.filename.rstrip("/"))
            if main_file not in names:
                raise DeployError("plugin_layout", "Plugin main file is missing from the ZIP.")
            bad = archive.testzip()
            if bad is not None:
                raise DeployError("plugin_zip", "Plugin ZIP integrity validation failed.")
            main_bytes = archive.read(main_file)
    except (zipfile.BadZipFile, RuntimeError, OSError) as exc:
        raise DeployError("plugin_zip", "Plugin ZIP is invalid.") from exc
    try:
        main_text = main_bytes.decode("utf-8")
    except UnicodeDecodeError as exc:
        raise DeployError("plugin_encoding", "Plugin main file must be UTF-8.") from exc
    if "Plugin Name:" not in main_text[:65536]:
        raise DeployError("plugin_layout", "Plugin main file has no WordPress plugin header.")
    return PluginPlan(
        zip_path=zip_path.resolve(),
        slug=slug,
        main_file=main_file,
        zip_sha256=sha256_bytes(raw_zip),
        main_text=main_text,
        main_sha256=text_sha256(main_text),
        allow_replace=allow_replace,
    )


def redact_text(value: str, secret_values: Iterable[str] = ()) -> str:
    safe = value
    for secret_value in secret_values:
        if secret_value:
            safe = safe.replace(secret_value, "[REDACTED]")
    safe = re.sub(
        r"(?i)(password|passwd|pwd|cookie|authorization|_wpnonce|nonce)(\s*[:=]\s*)[^\s,&]+",
        r"\1\2[REDACTED]",
        safe,
    )
    safe = re.sub(r"https?://[^\s?#]+[?#][^\s]+", "[REDACTED_URL]", safe)
    return safe[:500]


def _safe_evidence(value: Any, secrets_to_redact: Sequence[str]) -> Any:
    if value is None or isinstance(value, (bool, int, float)):
        return value
    if isinstance(value, str):
        return redact_text(value, secrets_to_redact)
    if isinstance(value, list):
        return [_safe_evidence(item, secrets_to_redact) for item in value]
    if isinstance(value, dict):
        result: dict[str, Any] = {}
        for key, item in value.items():
            if not re.fullmatch(r"[a-z0-9_]{1,64}", str(key)):
                raise DeployError("unsafe_evidence", "Evidence contains an unsafe key.")
            result[str(key)] = _safe_evidence(item, secrets_to_redact)
        return result
    raise DeployError("unsafe_evidence", "Evidence contains an unsupported value.")


class EvidenceRecorder:
    def __init__(
        self,
        base_dir: Path,
        *,
        mode: str,
        dry_run: bool,
        site_origin_hash: str,
        secrets_to_redact: Sequence[str] = (),
    ):
        if base_dir.exists() and base_dir.is_symlink():
            raise DeployError("unsafe_evidence", "Evidence directory must not be a symbolic link.")
        run_name = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ") + "-" + secrets.token_hex(4)
        self.run_dir = base_dir.resolve() / run_name
        self.run_dir.mkdir(parents=True, mode=0o700)
        try:
            os.chmod(self.run_dir, 0o700)
        except OSError:
            pass
        self.secrets = tuple(secrets_to_redact)
        self.document: dict[str, Any] = {
            "schema": 1,
            "mode": mode,
            "dry_run": dry_run,
            "site_origin_sha256": site_origin_hash,
            "started_at": utc_now(),
            "finished_at": None,
            "result": "running",
            "events": [],
        }
        self.flush()

    def flush(self) -> None:
        safe_document = _safe_evidence(self.document, self.secrets)
        atomic_write(
            self.run_dir / "evidence.json",
            (json.dumps(safe_document, indent=2, sort_keys=True) + "\n").encode("utf-8"),
        )

    def event(self, name: str, status: str, **details: Any) -> None:
        if not re.fullmatch(r"[a-z0-9_]{1,64}", name):
            raise DeployError("unsafe_evidence", "Evidence event name is unsafe.")
        if status not in {"planned", "ok", "skipped", "unavailable", "failed"}:
            raise DeployError("unsafe_evidence", "Evidence event status is unsafe.")
        self.document["events"].append(
            {
                "at": utc_now(),
                "name": name,
                "status": status,
                "details": _safe_evidence(details, self.secrets),
            }
        )
        self.flush()

    def save_manifest(self, theme: ThemePlan | None, plugin: PluginPlan | None) -> None:
        manifest: dict[str, Any] = {"schema": 1}
        if theme is not None:
            manifest["theme"] = {
                "slug": theme.slug,
                "files": [
                    {"path": item.remote_path, "sha256": item.sha256, "bytes": len(item.content)}
                    for item in theme.files
                ],
            }
        if plugin is not None:
            manifest["plugin"] = {
                "slug": plugin.slug,
                "main_file": plugin.main_file,
                "zip_name": plugin.zip_path.name,
                "zip_sha256": plugin.zip_sha256,
                "main_sha256": plugin.main_sha256,
                "replace_authorized": plugin.allow_replace,
            }
        atomic_write(
            self.run_dir / "manifest.json",
            (json.dumps(_safe_evidence(manifest, self.secrets), indent=2, sort_keys=True) + "\n").encode("utf-8"),
        )

    def finish(self, result: str) -> None:
        self.document["finished_at"] = utc_now()
        self.document["result"] = result
        self.flush()


def authenticate(client: HttpClient, credentials: Credentials) -> None:
    login_url = client.policy.endpoint("wp-login.php")
    login_page = client.request("Login page", login_url)
    parser = parse_html(login_page.text())
    forms = [form for form in parser.forms if "log" in form.fields and "pwd" in form.fields]
    if len(forms) != 1:
        raise DeployError("login_form", "WordPress login form was not found unambiguously.")
    form = forms[0]
    action = form_action(client.policy, login_page.final_url, form, "/wp-login.php")
    fields = {
        key: value
        for key, value in form.fields.items()
        if key in {"redirect_to", "testcookie", "rememberme", "wp-submit"}
    }
    fields.update(
        {
            "log": credentials.username,
            "pwd": credentials.password,
            "redirect_to": client.policy.endpoint("wp-admin/"),
            "testcookie": "1",
            "wp-submit": fields.get("wp-submit", "Log In"),
        }
    )
    client.request(
        "Login",
        action,
        method="POST",
        data=encode_form(fields),
        headers={"Content-Type": "application/x-www-form-urlencoded", "Referer": login_page.final_url},
    )
    admin = client.request("Admin verification", client.policy.endpoint("wp-admin/"))
    admin_parser = parse_html(admin.text())
    if "wp-admin" not in admin_parser.body_classes or any(
        "log" in item.fields and "pwd" in item.fields for item in admin_parser.forms
    ):
        raise DeployError("login_failed", "WordPress authentication could not be verified.")


def probe_capabilities(client: HttpClient, *, require_plugin_upload: bool = True) -> dict[str, bool]:
    editor = client.request(
        "Theme editor capability check",
        client.policy.endpoint("wp-admin/theme-editor.php"),
        allow_status={403},
    )
    editor_ok = False
    if editor.status == 200:
        parsed = parse_html(editor.text())
        editor_ok = any("newcontent" in form.textareas and bool(form.fields.get("nonce")) for form in parsed.forms)

    uploader = client.request(
        "Plugin upload capability check",
        client.policy.endpoint("wp-admin/plugin-install.php?tab=upload"),
        allow_status={403},
    )
    upload_ok = False
    if uploader.status == 200:
        parsed = parse_html(uploader.text())
        upload_ok = any("pluginzip" in form.file_inputs and bool(form.fields.get("_wpnonce")) for form in parsed.forms)

    export = client.request(
        "Export capability check",
        client.policy.endpoint("wp-admin/export.php"),
        allow_status={403},
    )
    export_ok = False
    if export.status == 200:
        for form in parse_html(export.text()).forms:
            try:
                action_url = client.policy.resolve(form.action or export.final_url, current_url=export.final_url)
            except DeployError:
                continue
            if urllib.parse.urlsplit(action_url).path.endswith("/wp-admin/export.php"):
                export_ok = True
                break
    if not editor_ok or (require_plugin_upload and not upload_ok):
        raise DeployError("insufficient_capability", "A capability required for this run is unavailable.")
    return {"theme_editor": editor_ok, "plugin_upload": upload_ok, "wxr_export": export_ok}


@dataclass(frozen=True, repr=False)
class ThemeSnapshot:
    remote_path: str
    content: str
    sha256: str
    nonce: str
    action_url: str


def parse_theme_editor(
    policy: SitePolicy,
    response: HttpResponse,
    *,
    theme_slug: str,
    remote_path: str,
) -> ThemeSnapshot:
    parser = parse_html(response.text())
    candidates = [form for form in parser.forms if "newcontent" in form.textareas and form.fields.get("nonce")]
    if len(candidates) != 1:
        raise DeployError("theme_editor_form", "Theme editor form was not found unambiguously.")
    form = candidates[0]
    if form.fields.get("file") != remote_path or form.fields.get("theme") != theme_slug:
        raise DeployError("theme_editor_identity", "Theme editor returned a different file or theme.")
    action = form_action(policy, response.final_url, form, "/wp-admin/theme-editor.php")
    content = form.textareas["newcontent"]
    return ThemeSnapshot(remote_path, content, text_sha256(content), form.fields["nonce"], action)


def fetch_theme_snapshot(client: HttpClient, theme_slug: str, remote_path: str) -> ThemeSnapshot:
    query = urllib.parse.urlencode({"file": remote_path, "theme": theme_slug})
    response = client.request(
        "Theme editor read",
        client.policy.endpoint("wp-admin/theme-editor.php?" + query),
    )
    return parse_theme_editor(client.policy, response, theme_slug=theme_slug, remote_path=remote_path)


def backup_theme(
    client: HttpClient,
    plan: ThemePlan,
    evidence: EvidenceRecorder,
) -> dict[str, ThemeSnapshot]:
    snapshots: dict[str, ThemeSnapshot] = {}
    backup_manifest: list[dict[str, Any]] = []
    for item in plan.files:
        snapshot = fetch_theme_snapshot(client, plan.slug, item.remote_path)
        snapshots[item.remote_path] = snapshot
        destination = evidence.run_dir / "backup" / "theme" / item.remote_path
        atomic_write(destination, snapshot.content.encode("utf-8"))
        backup_manifest.append(
            {"path": item.remote_path, "sha256": snapshot.sha256, "bytes": len(snapshot.content.encode("utf-8"))}
        )
        evidence.event("theme_file_backup", "ok", path=item.remote_path, sha256=snapshot.sha256)
    atomic_write(
        evidence.run_dir / "backup" / "theme-backup-manifest.json",
        (json.dumps({"schema": 1, "theme": plan.slug, "files": backup_manifest}, indent=2, sort_keys=True) + "\n").encode(),
    )
    return snapshots


def attempt_wxr_backup(client: HttpClient, evidence: EvidenceRecorder) -> bool:
    export_page = client.request(
        "WXR export page",
        client.policy.endpoint("wp-admin/export.php"),
        allow_status={403},
    )
    if export_page.status == 403:
        evidence.event("wxr_backup", "unavailable", reason="capability")
        return False
    parser = parse_html(export_page.text())
    candidates = []
    for form in parser.forms:
        action_url = client.policy.resolve(form.action or export_page.final_url, current_url=export_page.final_url)
        if urllib.parse.urlsplit(action_url).path.endswith("/wp-admin/export.php"):
            candidates.append((form, action_url))
    if not candidates:
        evidence.event("wxr_backup", "unavailable", reason="form_not_found")
        return False
    form, action_url = candidates[0]
    fields = {
        key: value
        for key, value in form.fields.items()
        if key in {"download", "content", "author", "start_date", "end_date", "status"}
    }
    fields["download"] = "true"
    fields["content"] = "all"
    separator = "&" if urllib.parse.urlsplit(action_url).query else "?"
    download_url = action_url + separator + urllib.parse.urlencode(fields)
    response = client.request("WXR export", download_url, maximum=MAX_WXR_BYTES)
    stripped = response.body.lstrip()
    if stripped.startswith(b"\xef\xbb\xbf"):
        stripped = stripped[3:].lstrip()
    if not stripped.startswith(b"<?xml") or b"<rss" not in stripped[:4096] or b"xmlns:wp=" not in stripped[:16384]:
        raise DeployError("wxr_invalid", "WordPress exposed an export form but did not return a valid WXR file.")
    destination = evidence.run_dir / "backup" / "wordpress-export.xml"
    atomic_write(destination, response.body)
    evidence.event("wxr_backup", "ok", sha256=sha256_bytes(response.body), bytes=len(response.body))
    return True


def safe_theme_order(files: Sequence[SourceFile]) -> list[SourceFile]:
    def key(item: SourceFile) -> tuple[int, int, str]:
        suffix = PurePosixPath(item.remote_path).suffix.lower()
        if item.remote_path == "functions.php":
            return (3, 0, item.remote_path)
        if suffix in {".css", ".js", ".png", ".jpg", ".jpeg", ".svg", ".webp"}:
            return (0, 0, item.remote_path)
        if suffix == ".php":
            return (2, 0, item.remote_path)
        return (1, 0, item.remote_path)

    return sorted(files, key=key)


def deploy_theme(
    client: HttpClient,
    plan: ThemePlan,
    backups: Mapping[str, ThemeSnapshot],
    evidence: EvidenceRecorder,
) -> None:
    preflight: dict[str, ThemeSnapshot] = {}
    for item in plan.files:
        current = fetch_theme_snapshot(client, plan.slug, item.remote_path)
        original = backups[item.remote_path]
        if current.sha256 != original.sha256:
            raise DeployError("remote_changed", "A remote theme file changed after backup; deployment stopped.")
        preflight[item.remote_path] = current
    evidence.event("theme_preflight", "ok", files=len(preflight))

    for item in safe_theme_order(plan.files):
        snapshot = preflight[item.remote_path]
        fields = {
            "action": "update",
            "file": item.remote_path,
            "theme": plan.slug,
            "newcontent": item.text(),
            "scrollto": "0",
            "nonce": snapshot.nonce,
        }
        client.request(
            "Theme editor update",
            snapshot.action_url,
            method="POST",
            data=encode_form(fields),
            headers={"Content-Type": "application/x-www-form-urlencoded"},
        )
        verified = fetch_theme_snapshot(client, plan.slug, item.remote_path)
        expected_sha = text_sha256(item.text())
        if verified.sha256 != expected_sha:
            raise DeployError("theme_verify", "A theme file did not match after deployment.")
        evidence.event("theme_file_deploy", "ok", path=item.remote_path, sha256=expected_sha)


def parse_plugin_upload_form(policy: SitePolicy, response: HttpResponse) -> tuple[ParsedForm, str]:
    parser = parse_html(response.text())
    candidates = [
        form
        for form in parser.forms
        if "pluginzip" in form.file_inputs and bool(form.fields.get("_wpnonce"))
    ]
    if len(candidates) != 1:
        raise DeployError("plugin_upload_form", "Plugin upload form was not found unambiguously.")
    form = candidates[0]
    action = form_action(policy, response.final_url, form, "/wp-admin/update.php")
    query = urllib.parse.parse_qs(urllib.parse.urlsplit(action).query)
    if query.get("action") != ["upload-plugin"]:
        raise DeployError("plugin_upload_form", "Plugin upload form action was unexpected.")
    return form, action


def find_plugin_overwrite_action(
    policy: SitePolicy,
    response: HttpResponse,
) -> tuple[ParsedForm | None, str] | None:
    parser = parse_html(response.text())
    matches: list[tuple[ParsedForm | None, str]] = []
    # WordPress core 5.5+ normally renders the replacement control as a
    # nonce-protected link. Keep a guarded form fallback for customized admin
    # UIs, but never infer or synthesize an overwrite URL.
    for href in parser.links:
        try:
            target = policy.resolve(href, current_url=response.final_url)
        except DeployError:
            continue
        parsed = urllib.parse.urlsplit(target)
        query = urllib.parse.parse_qs(parsed.query)
        if (
            parsed.path.endswith("/wp-admin/update.php")
            and query.get("action") == ["upload-plugin"]
            and query.get("overwrite", [""])[0] in {"update-plugin", "downgrade-plugin"}
            and query.get("_wpnonce")
        ):
            matches.append((None, target))
    for form in parser.forms:
        try:
            target = policy.resolve(form.action or response.final_url, current_url=response.final_url)
        except DeployError:
            continue
        parsed = urllib.parse.urlsplit(target)
        query = urllib.parse.parse_qs(parsed.query)
        if (
            parsed.path.endswith("/wp-admin/update.php")
            and query.get("action") == ["upload-plugin"]
            and query.get("overwrite", [""])[0] in {"update-plugin", "downgrade-plugin"}
            and (form.fields.get("_wpnonce") or form.fields.get("nonce"))
        ):
            matches.append((form, target))
    unique: dict[str, tuple[ParsedForm | None, str]] = {}
    for form, target in matches:
        unique[target] = (form, target)
    if len(unique) > 1:
        raise DeployError("plugin_overwrite_action", "Plugin replacement action was ambiguous.")
    return next(iter(unique.values())) if unique else None


def fetch_plugin_main_text(client: HttpClient, main_file: str) -> str:
    query = urllib.parse.urlencode({"file": main_file, "plugin": main_file})
    response = client.request(
        "Plugin editor verification",
        client.policy.endpoint("wp-admin/plugin-editor.php?" + query),
        allow_status={403},
    )
    if response.status == 403:
        raise DeployError("plugin_verify_capability", "Plugin editor verification capability is unavailable.")
    parser = parse_html(response.text())
    candidates = [form for form in parser.forms if "newcontent" in form.textareas]
    if len(candidates) != 1:
        raise DeployError("plugin_verify", "Installed plugin main file could not be read unambiguously.")
    form = candidates[0]
    if form.fields.get("file") not in {None, "", main_file}:
        raise DeployError("plugin_verify", "Plugin editor returned a different file.")
    if form.fields.get("plugin") not in {None, "", main_file}:
        raise DeployError("plugin_verify", "Plugin editor returned a different plugin.")
    return form.textareas["newcontent"]


def find_plugin_row(client: HttpClient, main_file: str) -> PluginRow | None:
    response = client.request("Plugin list verification", client.policy.endpoint("wp-admin/plugins.php"))
    rows = [row for row in parse_html(response.text()).plugin_rows if row.plugin_file == main_file]
    if len(rows) > 1:
        raise DeployError("plugin_list", "Expected plugin was not found unambiguously in WordPress.")
    return rows[0] if rows else None


def get_plugin_row(client: HttpClient, main_file: str) -> PluginRow:
    row = find_plugin_row(client, main_file)
    if row is None:
        raise DeployError("plugin_list", "Expected plugin was not found unambiguously in WordPress.")
    return row


def activate_plugin(client: HttpClient, main_file: str, evidence: EvidenceRecorder) -> None:
    row = get_plugin_row(client, main_file)
    if row.active:
        evidence.event("plugin_activate", "skipped", reason="already_active")
        return
    activation_links: list[str] = []
    for href in row.links:
        try:
            target = client.policy.resolve(href, current_url=client.policy.endpoint("wp-admin/plugins.php"))
        except DeployError:
            continue
        parsed = urllib.parse.urlsplit(target)
        query = urllib.parse.parse_qs(parsed.query)
        if (
            parsed.path.endswith("/wp-admin/plugins.php")
            and query.get("action") == ["activate"]
            and query.get("plugin") == [main_file]
            and query.get("_wpnonce")
        ):
            activation_links.append(target)
    if len(activation_links) != 1:
        raise DeployError("plugin_activation", "Safe activation link was not found unambiguously.")
    client.request("Plugin activation", activation_links[0])
    if not get_plugin_row(client, main_file).active:
        raise DeployError("plugin_activation", "Plugin activation could not be verified.")
    evidence.event("plugin_activate", "ok", main_file=main_file)


def deploy_plugin(client: HttpClient, plan: PluginPlan, evidence: EvidenceRecorder) -> None:
    existing_row = find_plugin_row(client, plan.main_file)
    if existing_row is not None:
        existing_main = fetch_plugin_main_text(client, plan.main_file)
        existing_sha = text_sha256(existing_main)
        atomic_write(
            evidence.run_dir / "backup" / "plugin" / plan.main_file,
            existing_main.encode("utf-8"),
        )
        evidence.event("plugin_main_backup", "ok", main_file=plan.main_file, sha256=existing_sha)
        if not plan.allow_replace:
            raise DeployError(
                "plugin_replace_not_authorized",
                "Plugin already exists; retain its previous ZIP and rerun with --allow-plugin-replace.",
            )
    else:
        evidence.event("plugin_main_backup", "skipped", reason="not_installed")

    upload_page = client.request(
        "Plugin upload page",
        client.policy.endpoint("wp-admin/plugin-install.php?tab=upload"),
    )
    form, action = parse_plugin_upload_form(client.policy, upload_page)
    fields = {
        key: value
        for key, value in form.fields.items()
        if key in {"_wpnonce", "_wp_http_referer"}
    }
    zip_data = read_limited_file(plan.zip_path, MAX_PLUGIN_ZIP_BYTES, "plugin_zip")
    body, content_type = encode_multipart(
        fields,
        file_field="pluginzip",
        filename=plan.zip_path.name,
        file_data=zip_data,
        content_type="application/zip",
    )
    response = client.request(
        "Plugin upload",
        action,
        method="POST",
        data=body,
        headers={"Content-Type": content_type, "Referer": upload_page.final_url},
    )
    overwrite = find_plugin_overwrite_action(client.policy, response)
    if overwrite is not None:
        if not plan.allow_replace:
            raise DeployError(
                "plugin_replace_not_authorized",
                "Plugin already exists; rerun with --allow-plugin-replace only after reviewing the backup.",
            )
        replace_form, replace_action = overwrite
        if replace_form is None:
            client.request("Plugin replacement", replace_action, headers={"Referer": response.final_url})
        else:
            allowed_names = {
                "_wpnonce",
                "nonce",
                "_wp_http_referer",
                "plugin",
                "package",
                "overwrite",
                "slug",
            }
            replace_fields = {
                key: value
                for key, value in replace_form.fields.items()
                if key in allowed_names and len(value) <= 4096
            }
            if not (replace_fields.get("_wpnonce") or replace_fields.get("nonce")):
                raise DeployError("plugin_overwrite_form", "Plugin replacement nonce is missing.")
            client.request(
                "Plugin replacement",
                replace_action,
                method="POST",
                data=encode_form(replace_fields),
                headers={"Content-Type": "application/x-www-form-urlencoded", "Referer": response.final_url},
            )
        evidence.event("plugin_replace", "ok", zip_sha256=plan.zip_sha256)
    else:
        evidence.event("plugin_upload", "ok", zip_sha256=plan.zip_sha256)

    installed_main = fetch_plugin_main_text(client, plan.main_file)
    if text_sha256(installed_main) != plan.main_sha256:
        raise DeployError("plugin_verify", "Installed plugin main file does not match the supplied ZIP.")
    evidence.event("plugin_verify", "ok", main_sha256=plan.main_sha256)
    activate_plugin(client, plan.main_file, evidence)


def find_speedycache_purge_links(policy: SitePolicy, response: HttpResponse) -> list[str]:
    matches: list[str] = []
    for href in parse_html(response.text()).links:
        try:
            target = policy.resolve(href, current_url=response.final_url)
        except DeployError:
            continue
        parsed = urllib.parse.urlsplit(target)
        query = urllib.parse.parse_qs(parsed.query)
        if (
            parsed.path.endswith("/wp-admin/admin-post.php")
            and query.get("action") == ["speedycache_delete_cache"]
            and query.get("_wpnonce")
        ):
            matches.append(target)
    return list(dict.fromkeys(matches))


def find_speedycache_purge_link(policy: SitePolicy, response: HttpResponse) -> str | None:
    """Backward-compatible primary-link helper used by existing tests/callers."""
    links = find_speedycache_purge_links(policy, response)
    if not links:
        return None
    primary = [link for link in links if "minified" not in urllib.parse.parse_qs(urllib.parse.urlsplit(link).query)]
    return primary[0] if primary else links[0]


def purge_speedycache(client: HttpClient, evidence: EvidenceRecorder) -> bool:
    candidates = [
        client.policy.endpoint("wp-admin/"),
        client.policy.endpoint("wp-admin/admin.php?page=speedycache"),
    ]
    purge_urls: list[str] = []
    for url in candidates:
        response = client.request("SpeedyCache discovery", url, allow_status={403, 404})
        if response.status != 200:
            continue
        purge_urls = find_speedycache_purge_links(client.policy, response)
        if purge_urls:
            break
    if not purge_urls:
        evidence.event("speedycache_purge", "unavailable", reason="action_not_discovered")
        return False
    for purge_url in purge_urls:
        client.request("SpeedyCache purge", purge_url)
    evidence.event("speedycache_purge", "ok", actions=len(purge_urls))
    return True


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(
        description="Back up and deploy an allow-listed WordPress theme/plugin change safely.",
    )
    mode = parser.add_mutually_exclusive_group(required=True)
    mode.add_argument("--check-login", action="store_true", help="Verify authentication and required capabilities.")
    mode.add_argument("--backup-only", action="store_true", help="Back up allow-listed theme files and attempt WXR export.")
    mode.add_argument("--deploy", action="store_true", help="Back up first, then deploy and verify.")
    parser.add_argument("--dry-run", action="store_true", help="Validate and write a plan without making any HTTP request.")
    parser.add_argument("--credentials-file", type=Path, help="0600 JSON file with site_url, username, and password.")
    parser.add_argument("--site-url", help="Site URL for dry-run only; credentials are not required.")
    parser.add_argument("--manifest", type=Path, help="Deployment manifest containing the exact theme file allow-list.")
    parser.add_argument("--plugin-zip", type=Path, help="Plugin ZIP to install or replace during --deploy.")
    parser.add_argument("--plugin-slug", help="Expected top-level directory in --plugin-zip.")
    parser.add_argument("--plugin-main-file", help="Expected plugin main file, including slug directory.")
    parser.add_argument(
        "--allow-plugin-replace",
        action="store_true",
        help="Authorize WordPress overwrite flow when the plugin already exists.",
    )
    parser.add_argument(
        "--evidence-dir",
        type=Path,
        default=Path(__file__).resolve().parent / "deploy-evidence",
        help="Local directory for mode-0600 backups and sanitized evidence.",
    )
    parser.add_argument("--timeout", type=float, default=25.0, help="Per-request timeout in seconds.")
    parser.add_argument("--ca-file", type=Path, help="Optional custom CA bundle.")
    parser.add_argument(
        "--allow-http-localhost",
        action="store_true",
        help="Permit HTTP only for localhost development; never permits remote HTTP.",
    )
    return parser


def validate_arguments(args: argparse.Namespace, parser: argparse.ArgumentParser) -> str:
    if args.timeout <= 0 or args.timeout > 120:
        parser.error("--timeout must be greater than zero and no more than 120 seconds")
    if args.site_url and not args.dry_run:
        parser.error("--site-url is allowed only with --dry-run")
    if args.site_url and args.credentials_file:
        parser.error("use either --site-url for dry-run or --credentials-file, not both")
    if args.check_login and args.manifest:
        parser.error("--check-login does not accept --manifest")
    if (args.backup_only or args.deploy) and args.manifest is None:
        parser.error("--manifest is required for --backup-only and --deploy")
    plugin_values = [args.plugin_zip, args.plugin_slug, args.plugin_main_file]
    if any(plugin_values) and not all(plugin_values):
        parser.error("--plugin-zip, --plugin-slug, and --plugin-main-file must be supplied together")
    if any(plugin_values) and not args.deploy:
        parser.error("plugin arguments are allowed only with --deploy")
    if args.allow_plugin_replace and not args.plugin_zip:
        parser.error("--allow-plugin-replace requires --plugin-zip")
    if args.ca_file is not None and (args.ca_file.is_symlink() or not args.ca_file.is_file()):
        parser.error("--ca-file is missing or unsafe")
    if args.check_login:
        return "check_login"
    if args.backup_only:
        return "backup_only"
    return "deploy"


def site_policy_for_run(args: argparse.Namespace) -> tuple[SitePolicy, Credentials | None]:
    if args.dry_run and args.site_url:
        return SitePolicy.from_url(args.site_url, allow_http_localhost=args.allow_http_localhost), None
    credentials = load_credentials(args.credentials_file, allow_http_localhost=args.allow_http_localhost)
    return SitePolicy.from_url(credentials.site_url, allow_http_localhost=args.allow_http_localhost), credentials


def main(argv: Sequence[str] | None = None) -> int:
    parser = build_parser()
    args = parser.parse_args(argv)
    mode = validate_arguments(args, parser)
    repo_root = Path(__file__).resolve().parents[1]
    evidence: EvidenceRecorder | None = None
    credentials: Credentials | None = None
    try:
        theme_plan = load_theme_plan(args.manifest, repo_root) if args.manifest else None
        plugin_plan = None
        if args.plugin_zip:
            plugin_plan = validate_plugin_zip(
                args.plugin_zip,
                slug=args.plugin_slug,
                main_file=args.plugin_main_file,
                allow_replace=args.allow_plugin_replace,
            )
        policy, credentials = site_policy_for_run(args)
        secrets_to_redact = (credentials.username, credentials.password) if credentials else ()
        origin_hash = sha256_bytes(policy.base_url.encode("utf-8"))
        evidence = EvidenceRecorder(
            args.evidence_dir,
            mode=mode,
            dry_run=args.dry_run,
            site_origin_hash=origin_hash,
            secrets_to_redact=secrets_to_redact,
        )
        evidence.save_manifest(theme_plan, plugin_plan)

        if args.dry_run:
            evidence.event(
                "deployment_plan",
                "planned",
                theme_files=len(theme_plan.files) if theme_plan else 0,
                plugin=plugin_plan is not None,
                network_requests=0,
            )
            evidence.finish("dry_run")
            print(f"DRY RUN OK: sanitized plan saved in {evidence.run_dir}")
            return 0

        if credentials is None:
            raise DeployError("credentials_missing", "Credentials are required outside dry-run mode.")
        client = HttpClient(policy, timeout=args.timeout, ca_file=args.ca_file)
        authenticate(client, credentials)
        evidence.event("login", "ok")
        capabilities = probe_capabilities(
            client,
            require_plugin_upload=(mode == "check_login" or plugin_plan is not None),
        )
        evidence.event("capability_check", "ok", **capabilities)
        if mode == "check_login":
            evidence.finish("success")
            print(f"LOGIN CHECK OK: sanitized evidence saved in {evidence.run_dir}")
            return 0

        if theme_plan is None:
            raise DeployError("manifest_missing", "Theme deployment manifest is required.")
        backups = backup_theme(client, theme_plan, evidence)
        attempt_wxr_backup(client, evidence)
        evidence.event("mutation_gate", "ok", theme_backups=len(backups))
        if mode == "backup_only":
            evidence.finish("success")
            print(f"BACKUP OK: backups and sanitized evidence saved in {evidence.run_dir}")
            return 0

        if plugin_plan is not None:
            deploy_plugin(client, plugin_plan, evidence)
        else:
            evidence.event("plugin_deploy", "skipped", reason="not_requested")
        deploy_theme(client, theme_plan, backups, evidence)
        purge_speedycache(client, evidence)
        evidence.finish("success")
        print(f"DEPLOY OK: sanitized evidence saved in {evidence.run_dir}")
        return 0
    except DeployError as exc:
        if evidence is not None:
            try:
                evidence.event("run", "failed", code=exc.code)
                evidence.finish("failed")
            except Exception:
                pass
        print(f"ERROR [{exc.code}]: {exc.safe_message}", file=sys.stderr)
        return 2
    except Exception:
        if evidence is not None:
            try:
                evidence.event("run", "failed", code="internal_error")
                evidence.finish("failed")
            except Exception:
                pass
        print("ERROR [internal_error]: Unexpected internal failure; no diagnostic body or secret was printed.", file=sys.stderr)
        return 3
    finally:
        credentials = None


if __name__ == "__main__":
    raise SystemExit(main())
