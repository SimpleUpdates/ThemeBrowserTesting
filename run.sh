#!/usr/bin/env bash -x

THEME_PATH=$( cd "$1" ; pwd -P )
docker-compose build app
docker-compose run -v ${THEME_PATH}:/theme app