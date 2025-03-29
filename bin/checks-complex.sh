#!/usr/bin/env bash

PROJECT_DIR="$(dirname $(dirname $0))"
SYMFONY_VERSIONS='7.2 7.1 7.0 6.4 6.3 6.2 6.1 6.0 5.4'

cd "${PROJECT_DIR}" || exit 1

for SYMFONY_VERSION in $SYMFONY_VERSIONS; do
  echo "Run checks on Symfony ${SYMFONY_VERSION}" && \
  bin/install-specific-symfony-version.sh "${SYMFONY_VERSION}.*" && \
  composer run checks && \
  echo || \
  echo '--------------------------------------------------------------------------------' && \
  echo || \
  exit 1
done
