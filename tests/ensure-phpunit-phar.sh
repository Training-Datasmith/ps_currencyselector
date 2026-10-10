#!/usr/bin/env bash
set -euo pipefail

PHAR=/tmp/phpunit-4.8.36.phar
ASC=/tmp/phpunit-4.8.36.phar.asc
EXPECTED_SHA='ab8bc3017a64cf75a2112f5e089a1d5b0c4cc2116556d60c1af5e3f584e85c5e'
EXPECTED_FP='D8406D0D82947747293778314AA394086372C20A'

curl -fsSL -o "$PHAR" https://phar.phpunit.de/phpunit-4.8.36.phar
curl -fsSL -o "$ASC" https://phar.phpunit.de/phpunit-4.8.36.phar.asc

gpg --keyserver hkps://keys.openpgp.org --recv-keys 4AA394086372C20A

FP="$(gpg --with-colons --fingerprint 4AA394086372C20A 2>/dev/null | awk -F: '$1=="fpr"{print $10; exit}')"
if [[ "$FP" != "$EXPECTED_FP" ]]; then
  echo "Unexpected GPG fingerprint: $FP" >&2
  exit 1
fi

gpg --verify "$ASC" "$PHAR"

ACTUAL_SHA="$(sha256sum "$PHAR" | awk '{print $1}')"
if [[ "$ACTUAL_SHA" != "$EXPECTED_SHA" ]]; then
  echo "Unexpected SHA-256: $ACTUAL_SHA" >&2
  exit 1
fi

echo "PHPUnit PHAR verified at $PHAR"
