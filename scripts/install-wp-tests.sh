#!/usr/bin/env bash
# Back-compat wrapper: use bin/4wp-install-wp-tests (vendor/bin/4wp-install-wp-tests in plugins).
exec "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/bin/4wp-install-wp-tests" "$@"
