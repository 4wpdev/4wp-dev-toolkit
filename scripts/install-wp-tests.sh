#!/usr/bin/env bash
# Install WordPress PHPUnit test suite (local / CI only — do not ship inside plugin ZIP).
set -euo pipefail

DB_NAME="${WORDPRESS_DB_NAME:-wordpress_tests}"
DB_USER="${WORDPRESS_DB_USER:-root}"
DB_PASS="${WORDPRESS_DB_PASSWORD:-root}"
DB_HOST="${WORDPRESS_DB_HOST:-localhost}"
WP_VERSION="${WP_VERSION:-latest}"
TMPDIR="${TMPDIR:-/tmp}"

if [ -z "${WP_TESTS_DIR:-}" ]; then
	WP_TESTS_DIR="${TMPDIR}/wordpress-tests-lib"
	export WP_TESTS_DIR
fi

if [ ! -d "$WP_TESTS_DIR/includes" ]; then
	mkdir -p "$WP_TESTS_DIR"
	bash -c "cd '$WP_TESTS_DIR' && svn co --quiet https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes includes"
	bash -c "cd '$WP_TESTS_DIR' && svn co --quiet https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/data data"
fi

if [ ! -f "$WP_TESTS_DIR/wp-tests-config.php" ]; then
	cat >"$WP_TESTS_DIR/wp-tests-config.php" <<EOF
<?php
define( 'DB_NAME', '$DB_NAME' );
define( 'DB_USER', '$DB_USER' );
define( 'DB_PASSWORD', '$DB_PASS' );
define( 'DB_HOST', '$DB_HOST' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );
define( 'WP_PHP_BINARY', 'php' );
define( 'WPLANG', '' );
define( 'WP_DEBUG', true );
\$table_prefix = 'wptests_';
EOF
fi

echo "WP_TESTS_DIR=$WP_TESTS_DIR"
