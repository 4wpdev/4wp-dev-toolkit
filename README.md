# 4WP Dev Toolkit

Shared **WordPress Coding Standards (PHPCS)** and **PHPUnit** baseline for `4wp-*` plugins.

- **Not** a wordpress.org product — do not upload to the plugin directory.
- **Not** activated on production sites — keep in `wp-content/plugins/` only for local/CI path repos.

## Contents

| Path | Purpose |
|------|---------|
| `phpcs.xml.dist` | Base WPCS + PHPCompatibilityWP ruleset |
| `phpunit.xml.dist` | PHPUnit 9 template |
| `tests/bootstrap.php.dist` | Bootstrap template for plugin tests |
| `scripts/install-wp-tests.sh` | WP integration test suite (dev/CI only) |
| `docs/SETUP.md` | Adopt in a plugin |
| `.github/workflows/php-quality.yml.dist` | GitHub Actions template |

## Course

[4wp-cursor-rules: WordPress Coding Standards](../../../4wp-cursor-rules/course/wordpress-coding-standards/README.md)

## License

GPL-2.0-or-later
