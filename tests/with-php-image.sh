#!/usr/bin/env bash
set -euo pipefail

PHP_IMAGE_54_DIGEST='php@sha256:d9eaac6344eb9399f053ef6b538a237101d686d866fb484ad4834bfada6afbd9'
PHP_IMAGE_56_DIGEST='php@sha256:6ce95208609dc66df163ab936c970b3b34cd901b85c747102c5999f08ade9143'

ROOT="$(cd "$(dirname "$0")/.." && pwd)"

DOCKER="${DOCKER_CMD:-sudo docker}"

pick_image() {
  if $DOCKER image inspect "$PHP_IMAGE_54_DIGEST" >/dev/null 2>&1; then
    echo "$PHP_IMAGE_54_DIGEST"
    return 0
  fi

  if $DOCKER pull "$PHP_IMAGE_54_DIGEST" >/dev/null 2>&1; then
    echo "$PHP_IMAGE_54_DIGEST"
    return 0
  fi

  echo "PHP 5.4 digest image unavailable; using PHP 5.6.40 fallback" >&2
  $DOCKER pull "$PHP_IMAGE_56_DIGEST" >/dev/null
  echo "$PHP_IMAGE_56_DIGEST"
}

IMAGE="$(pick_image)"

verify_php_version() {
  local image="$1"
  local version
  version="$($DOCKER run --rm "$image" php -r 'echo PHP_VERSION;')"
  if [[ "$version" == 5.4.* ]]; then
    if [[ "$version" != "5.4.45" ]]; then
      echo "Unexpected PHP 5.4 version: $version" >&2
      exit 1
    fi
    echo "Using PHP $version (5.4 digest)" >&2
    return 0
  fi
  if [[ "$version" == "5.6.40" ]]; then
    echo "Using PHP $version (5.6.40 fallback)" >&2
    return 0
  fi
  echo "Unexpected PHP version: $version" >&2
  exit 1
}

verify_php_version "$IMAGE"

export PHP_RUNNER="$DOCKER run --rm -e PHPUNIT_PHAR=/tmp/phpunit-4.8.36.phar -e PHP_BINARY=php -v $ROOT:/work -v /tmp/phpunit-4.8.36.phar:/tmp/phpunit-4.8.36.phar:ro -w /work/tests $IMAGE php"

if [[ "${1:-}" == "--export-only" ]]; then
  echo "$PHP_RUNNER"
  exit 0
fi

if [[ $# -gt 0 ]]; then
  # shellcheck disable=SC2086
  ${PHP_RUNNER} "$@"
fi
