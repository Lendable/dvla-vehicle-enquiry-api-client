#!/usr/bin/env bash

set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_NAME="lendable_dvla_vehicle_enquiry_api_client_$(uuidgen | tr "[:upper:]" "[:lower:]" | sed 's/-//g')"

DOCKER_COMPOSE="docker-compose \
  -f ${DIR}/docker-compose-tests.yaml
  -p ${PROJECT_NAME}"

export DOCKER_BUILDKIT=1
docker build --pull --ssh default -f "${DIR}/../Dockerfile.php-cli" "${DIR}/../" -t "${PROJECT_NAME}_runner:latest"

if ! ${DOCKER_COMPOSE} up --abort-on-container-exit; then
  EXIT_CODE=1
fi

${DOCKER_COMPOSE} down -t 0

exit ${EXIT_CODE:-0}