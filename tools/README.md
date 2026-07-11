# Safe WordPress deployment helper

`wp_deploy.py` backs up and deploys an explicit allow-list of custom-theme
files through the WordPress admin UI. It can also install or replace and
activate one validated plugin ZIP. It uses only Python's standard library.

The helper does not store cookies, print page bodies, or put credentials in
its evidence. Every non-dry deployment authenticates, verifies capabilities,
backs up every allow-listed remote theme file, and attempts a WXR export
before the first mutation. A remote file change between backup and mutation
stops the run.

## Credentials

Use either environment variables:

```sh
export WP_DEPLOY_SITE_URL='https://example.invalid'
export WP_DEPLOY_USERNAME='operator'
export WP_DEPLOY_PASSWORD='use-a-secret-manager'
```

or a JSON file with mode `0600`:

```json
{
  "site_url": "https://example.invalid",
  "username": "operator",
  "password": "use-a-secret-manager"
}
```

Never commit the credential file. If any deployment credential was ever
stored in a repository or agent memory, rotate it.

## Manifest

Copy `deploy-manifest.example.json` and list exactly the theme files intended
for that run. Paths are relative to the repository root and the theme root;
absolute paths, traversal, symlinks, missing files, duplicates, and unknown
manifest keys are rejected.

## Commands

Dry run. This validates local inputs, writes a sanitized manifest/evidence
record, and creates no HTTP client:

```sh
python3 tools/wp_deploy.py --backup-only --dry-run \
  --site-url https://example.invalid \
  --manifest tools/deploy-manifest.json
```

Verify login plus Theme File Editor and plugin-upload capabilities:

```sh
python3 tools/wp_deploy.py --check-login \
  --credentials-file /secure/path/wp-credentials.json
```

Back up the allow-listed live theme files and attempt an all-content WXR
export without changing WordPress:

```sh
python3 tools/wp_deploy.py --backup-only \
  --credentials-file /secure/path/wp-credentials.json \
  --manifest tools/deploy-manifest.json
```

Deploy theme files and a new plugin:

```sh
python3 tools/wp_deploy.py --deploy \
  --credentials-file /secure/path/wp-credentials.json \
  --manifest tools/deploy-manifest.json \
  --plugin-zip /secure/path/interagents-booking-calendar.zip \
  --plugin-slug interagents-booking-calendar \
  --plugin-main-file interagents-booking-calendar/interagents-booking-calendar.php
```

If the plugin already exists, its current main file is saved in the run backup
and the helper stops before upload unless `--allow-plugin-replace` was supplied
explicitly. Keep the complete previous plugin ZIP separately because the admin
UI cannot produce a full plugin archive. After upload the helper reads the
installed main file through Plugin File Editor, compares its SHA-256 with the
ZIP, activates the exact expected plugin entry, and verifies the active row.

After theme verification, the helper searches for the nonce-protected
`speedycache_delete_cache` admin action and invokes it if discoverable. A
missing SpeedyCache action is recorded as unavailable; a discovered action
that fails makes the run fail.

## Evidence and backups

Runtime output defaults to `tools/deploy-evidence/`, which is ignored by Git.
Directories use mode `0700`; files use mode `0600`. `evidence.json` and
`manifest.json` contain controlled statuses, file paths, byte counts, and
hashes—not usernames, passwords, cookies, nonces, page bodies, or raw URLs.
Raw theme and WXR backups are kept separately under the run's `backup/`
directory.

## Tests

Tests use only local temporary files and mocked HTTP clients:

```sh
python3 -m unittest discover -s tools/tests -v
```
