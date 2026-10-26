# Changelog

## 1.0.0 — 2026-10-26

### Added
- PHPCS standard **`4WP`** (`4WP/ruleset.xml`); package type `phpcodesniffer-standard` — plugins use `<rule ref="4WP"/>` from `vendor/`, no sibling clone needed.
- `bin/4wp-mcp-smoke` — MCP smoke test for server `4wp` (initialize → tools/list → tools/call), scenarios, PASS / FAIL / SKIP, exit codes.
- `scenarios/4wp-social.json`, `scenarios/4wp-seo-helper.json` — read-only MCP calls.
- `bin/4wp-family-check` — lint / test across all `4wp-*` plugins with a summary table.
- `src/Testing/UnitTestCase.php` (Brain Monkey) and `src/Testing/AbilityAssertions.php` (ability contract, permissions).
- `tests/ExampleTest.php.dist`, integration suite in `phpunit.xml.dist`, `templates/.wp-env.json.dist`.
- `.env.dist` — every script configurable via env / `.env`.

### Changed
- `bin/4wp-install-wp-tests` replaces the old script: resolves `WP_VERSION=latest` (there is no svn `tags/latest`), downloads WordPress core, writes `wp-tests-config.php` from the official sample, supports DB sockets, optional DB create. `scripts/install-wp-tests.sh` forwards to it.
- `tests/bootstrap.php.dist` — unit (Brain Monkey) and integration (WP_TESTS_DIR) modes.
- CI template: plugin in `plugin/`, toolkit beside it (fixes `../4wp-dev-toolkit` paths), PHP matrix via setup-php instead of hard-coded 7.4.
- `yoast/phpunit-polyfills` `^2.0 || ^4.0`.
- README / SETUP rewritten: how it works, env configuration, recommended vs legacy setup.

### Removed
- `index.php` — the toolkit is not a WordPress plugin.
- Broken relative link to the course.

## 0.1.0

- Initial PHPCS / PHPUnit baseline (`338e47e`).
