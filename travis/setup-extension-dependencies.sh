#!/bin/bash --

set -e
set -x

EXTNAME="${1}"
EXTDEPS="${2}"

# Check if package have dependencies in the
# 'require' object, inside the composer.json file
if [[ "${EXTDEPS}" == '1' ]]; then
	# Fix for Composer 2
	composer require \
		--working-dir=phpBB/ext/"${EXTNAME}" \
		--prefer-dist \
		--update-with-all-dependencies \
		'composer/package-versions-deprecated:^1.11.99' \
		'ocramius/proxy-manager:~2.1.1'

	composer install \
		--working-dir=phpBB/ext/"${EXTNAME}" \
		--prefer-dist \
		--no-dev \
		--no-interaction
fi
