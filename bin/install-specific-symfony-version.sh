#!/usr/bin/env bash

PROJECT_DIR="$(dirname $(dirname $0))"
SYMFONY_VERSION="${1}"

cd "${PROJECT_DIR}" || exit 1

if [ ! -d vendor ]; then
  composer install
fi

if [ -z "${SYMFONY_VERSION}" ]; then
  rm composer.lock && composer install
else
  composer update\
    "symfony/config:${SYMFONY_VERSION}" \
    "symfony/dependency-injection:${SYMFONY_VERSION}" \
    "symfony/error-handler:${SYMFONY_VERSION}" \
    "symfony/event-dispatcher:${SYMFONY_VERSION}" \
    "symfony/filesystem:${SYMFONY_VERSION}" \
    "symfony/http-foundation:${SYMFONY_VERSION}" \
    "symfony/http-kernel:${SYMFONY_VERSION}" \
    "symfony/serializer:${SYMFONY_VERSION}" \
    "symfony/var-dumper:${SYMFONY_VERSION}" \
    "symfony/var-exporter:${SYMFONY_VERSION}" \
    --with-all-dependencies
fi
