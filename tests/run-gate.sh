#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
PHAR="${PHPUNIT_PHAR:-/tmp/phpunit-4.8.36.phar}"

if [[ -z "${PHP_RUNNER:-}" ]]; then
  echo "PHP_RUNNER is not set (use tests/with-php-image.sh)" >&2
  exit 1
fi

run_php() {
  # shellcheck disable=SC2086
  ${PHP_RUNNER} "$@"
}

if [[ ! -r "$PHAR" ]]; then
  echo "Missing PHPUnit PHAR at $PHAR" >&2
  exit 1
fi

echo "=== Gate pass 1: default order ==="
run_php "$PHAR" -c phpunit.xml

echo "=== Gate pass 2: default order ==="
run_php "$PHAR" -c phpunit.xml

echo "=== Gate pass 3: shuffle seed 20261008 ==="
run_php run-shuffled.php

echo "=== Gate pass 4: shuffle seed 20261008 ==="
run_php run-shuffled.php

echo "Gate completed successfully."
