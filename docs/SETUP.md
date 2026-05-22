# 4WP Dev Toolkit — setup

Dev-only Composer package. **Not** distributed in wordpress.org plugin ZIPs.

## Per plugin (example: 4wp-weather)

1. Add path repository in `composer.json`:

```json
"repositories": [
  { "type": "path", "url": "../4wp-dev-toolkit", "options": { "symlink": true } }
],
"require-dev": {
  "4wpdev/4wp-dev-toolkit": "@dev"
}
```

2. Copy or symlink configs:

- `phpcs.xml` → extends `../4wp-dev-toolkit/phpcs.xml.dist`
- `phpunit.xml` → from `phpunit.xml.dist` in plugin
- `tests/bootstrap.php` → from `tests/bootstrap.php.dist`

3. Install and run:

```bash
cd wp-content/plugins/4wp-weather
composer install
composer run lint
composer run test
```

## Git

- Toolkit: own repo `4wpdev/4wp-dev-toolkit` (clone beside plugins).
- Plugin repo: `tests/`, `phpcs.xml`, `phpunit.xml` stay in **git**; `.distignore` excludes them from release ZIP.

## CI (next step)

See `.github/workflows/php-quality.yml.dist` — GitHub Actions on push/PR.
