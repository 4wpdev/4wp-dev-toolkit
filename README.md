# 4WP Dev Toolkit

One place for code quality and tests of all `4wp-*` plugins: shared **PHPCS standard `4WP`**, **PHPUnit** helpers, **MCP smoke tests** for abilities, and a **family check** that runs lint/tests across every plugin.

- Dev-only Composer package — **not** a WordPress plugin, not for wordpress.org, never activated on a site.
- Installed per plugin into `vendor/` (`require-dev`). No need to clone it beside the plugins, except to develop the toolkit itself.

## How it works

| Layer | What | Where it lives |
|---|---|---|
| Rules | PHPCS standard `4WP` = WPCS + PHPCompatibilityWP | `4WP/ruleset.xml` → plugin `phpcs.xml`: `<rule ref="4WP"/>` |
| Unit tests | Brain Monkey base class, bootstrap template, example | `src/Testing/UnitTestCase.php`, `tests/*.dist` |
| Integration tests | WordPress test suite installer, wp-env template, ability assertions | `bin/4wp-install-wp-tests`, `templates/.wp-env.json.dist`, `src/Testing/AbilityAssertions.php` |
| MCP / abilities | Smoke test against server `4wp` (4wp-mcp-connector) + per-plugin scenarios | `bin/4wp-mcp-smoke`, `scenarios/*.json` |
| Whole family | `composer run lint` / `test` in every `4wp-*`, summary table | `bin/4wp-family-check` |
| CI | GitHub Actions template (PHP matrix) | `.github/workflows/php-quality.yml.dist` |

Plugin-specific tests stay in each plugin's `tests/` (they ship with the plugin's code); shared tools, scenarios and runners live here.

## Quick start (in a plugin)

```bash
composer config repositories.4wp-dev-toolkit vcs https://github.com/4wpdev/4wp-dev-toolkit
composer require --dev 4wpdev/4wp-dev-toolkit:dev-main
```

`phpcs.xml`:

```xml
<rule ref="4WP"/>
<config name="testVersion" value="8.3-"/>
<config name="minimum_supported_wp_version" value="6.9"/>
```

Details, legacy `../4wp-dev-toolkit` setup and local toolkit development: **[docs/SETUP.md](docs/SETUP.md)**.

## Commands

| Command | What |
|---|---|
| `composer run lint` / `lint:fix` | PHPCS in the plugin (`4WP` standard) |
| `composer run test` | PHPUnit (unit; integration when `WP_TESTS_DIR` is set) |
| `vendor/bin/4wp-install-wp-tests` | WordPress core + test library for integration tests |
| `vendor/bin/4wp-mcp-smoke` | List tools on server `4wp` |
| `vendor/bin/4wp-mcp-smoke 4wp-seo-helper` | Run `scenarios/4wp-seo-helper.json` (read-only calls) |
| `vendor/bin/4wp-mcp-smoke --all` | All scenarios |
| `vendor/bin/4wp-mcp-smoke --tool=<name> --args='{"k":"v"}'` | One call |
| `vendor/bin/4wp-family-check [lint\|test]` | All `4wp-*` plugins, summary table |

In the toolkit repo itself: `composer run mcp-smoke -- --all`, `composer run family-check -- lint`.

## Configuration (env)

Every script reads shell env first, then `.env` (copy `.env.dist`; `.env` is git-ignored).

| Variable | Used by | Default |
|---|---|---|
| `WP_MCP_URL`, `WP_USER`, `WP_APP_PASSWORD` | mcp-smoke | — (required) |
| `WP_MCP_INSECURE=1` | mcp-smoke | `0` — only for Local self-signed TLS |
| `FORWP_PLUGINS_DIR` | family-check | parent dir of the toolkit |
| `FORWP_PLUGINS` | family-check | every `4wp-*` with `composer.json` |
| `FORWP_FAMILY_INSTALL=1` | family-check | `0` — `composer install` where `vendor/` is missing |
| `WP_VERSION`, `WP_TESTS_DIR`, `WP_CORE_DIR` | install-wp-tests | `latest`, `$TMPDIR/wordpress-tests-lib`, `$TMPDIR/wordpress` |
| `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_HOST` | install-wp-tests | `wordpress_tests`, `root`, `root`, `localhost` (supports `localhost:/path/mysql.sock`) |

## MCP scenarios

`scenarios/<plugin>.json`:

```json
{
	"plugin": "4wp-social",
	"calls": [
		{ "tool": "forwp-social-platforms", "arguments": {}, "readonly": true, "expect_keys": [ "result" ] }
	]
}
```

Tool name = ability name with `/` → `-`. Result per call: **PASS**, **FAIL**, or **SKIP** (tool not enabled in Settings → 4WP MCP, or a write call without `--allow-write`). Exit code `1` on any FAIL.

## Ability assertions (integration tests)

```php
final class AbilitiesTest extends WP_UnitTestCase {
	use \ForWP\DevToolkit\Testing\AbilityAssertions;

	public function test_get_meta_contract(): void {
		$this->assertAbilityContract( 'forwp-seo-helper/get-meta', 'forwp-seo-helper', true );
		$this->assertAbilityPermission( 'forwp-seo-helper/get-meta', 0, false );
	}
}
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

GPL-2.0-or-later
