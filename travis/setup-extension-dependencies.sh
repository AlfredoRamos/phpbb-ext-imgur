#!/bin/bash --

set -e
set -x

EXTNAME="${1}"

cd phpBB/ext/"${EXTNAME}"
composer install --prefer-dist --no-dev --no-interaction
cd ../../../../
