# 4WP Dev Toolkit — setup

Dev-only Composer package (`type: phpcodesniffer-standard`). **Not** distributed in wordpress.org plugin ZIPs.

## Recommended: from GitHub into vendor/

Works the same locally, in CI and on any machine — no sibling clone needed.

1. `composer.json` of the plugin:

```json
"repositories": [
  { "type": "vcs", "url": "https://github.com/4wpdev/4wp-dev-toolkit" }
],
"require-dev": {
  "4wpdev/4wp-dev-toolkit": "dev-main"
},
"config": {
  "allow-plugins": { "dealerdirect/phpcodesniffer-composer-installer": true }
},
"scripts": {
  "lint": "phpcs",
  "lint:fix": "phpcbf",
  "test": "phpunit"
}
```

The toolkit brings WPCS, PHPCompatibilityWP, PHPUnit 9, Brain Monkey and PHPUnit Polyfills — do not require them again in the plugin.

2. `phpcs.xml` of the plugin:

```xml
<?xml version="1.0"?>
<ruleset name="4WP Example">
	<rule ref="4WP"/>

	<file>example.php</file>
	<file>src</file>
	<exclude-pattern>*/vendor/*</exclude-pattern>

	<!-- Raise the baseline (defaults: PHP 7.4-, WP 6.4). -->
	<config name="testVersion" value="8.3-"/>
	<config name="minimum_supported_wp_version" value="6.9"/>

	<config name="text_domain" value="4wp-example"/>
	<rule ref="WordPress.NamingConventions.PrefixAllGlobals">
		<properties>
			<property name="prefixes" type="array">
				<element value="forwp_example"/>
				<element value="ForWP\Example"/>
			</property>
		</properties>
	</rule>
</ruleset>
```

3. Tests: copy `vendor/4wpdev/4wp-dev-toolkit/phpunit.xml.dist` → `phpunit.xml.dist`, `tests/bootstrap.php.dist` → `tests/bootstrap.php`, `tests/ExampleTest.php.dist` → `tests/unit/ExampleTest.php`.

4. `composer install && composer run lint && composer run test`.

## Integration tests

Option A — test suite on the host (Local / any MySQL):

```bash
export DB_HOST="localhost:/path/to/mysql.sock"   # Local: Site → Database → Socket
vendor/bin/4wp-install-wp-tests                  # WP_VERSION=latest by default
WP_TESTS_DIR="${TMPDIR%/}/wordpress-tests-lib" composer run test -- --testsuite integration
```

Option B — Docker via wp-env: copy `templates/.wp-env.json.dist` → `.wp-env.json`, then `npx wp-env start` and `npx wp-env run tests-cli --env-cwd=wp-content/plugins/<plugin> vendor/bin/phpunit`.

Use `\ForWP\DevToolkit\Testing\AbilityAssertions` in `WP_UnitTestCase` for abilities.

## MCP smoke tests

Needs 4wp-mcp-connector + MCP Adapter on the site and the tools enabled in Settings → 4WP MCP.

```bash
cp vendor/4wpdev/4wp-dev-toolkit/.env.dist .env   # fill WP_MCP_URL, WP_USER, WP_APP_PASSWORD; add .env to .gitignore
vendor/bin/4wp-mcp-smoke                           # list tools
vendor/bin/4wp-mcp-smoke 4wp-social                # scenario
```

New plugin scenario: add `scenarios/<plugin>.json` to the toolkit (read-only calls by default).

## Developing the toolkit locally

```bash
cd /Users/admin/Projects/4wpdev            # beside the plugins
git clone https://github.com/4wpdev/4wp-dev-toolkit.git
cd 4wp-dev-toolkit && composer install
composer run lint
composer run family-check -- lint
```

To try unreleased toolkit changes in one plugin, point it to the local copy temporarily (do not commit):

```bash
composer config repositories.4wp-dev-toolkit path ../4wp-dev-toolkit
composer update 4wpdev/4wp-dev-toolkit
# revert: composer config repositories.4wp-dev-toolkit vcs https://github.com/4wpdev/4wp-dev-toolkit
```

## Legacy setup (still supported)

Plugins with `<rule ref="../4wp-dev-toolkit/phpcs.xml.dist"/>` and a `path` repository `../4wp-dev-toolkit` keep working while the toolkit is cloned beside them. `phpcs.xml.dist` now just includes `4WP/ruleset.xml`; `scripts/install-wp-tests.sh` forwards to `bin/4wp-install-wp-tests`. Migrate with the two edits above.

## Git

- Plugin repo: `tests/`, `phpcs.xml`, `phpunit.xml.dist` stay in **git**; `.distignore` excludes them from the release ZIP.
- `.env` is never committed.

## CI

`.github/workflows/php-quality.yml.dist` → `.github/workflows/php-quality.yml`: checks out the plugin into `plugin/` and the toolkit beside it (both setups work), PHP matrix, PHPCS, PHPUnit.
