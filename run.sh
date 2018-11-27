#!/usr/bin/env bash -x

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
THEME_PATH=$( cd "$1" ; pwd -P )

chmod +x ${DIR}/app.php

docker-compose run -v ${THEME_PATH}:/theme app