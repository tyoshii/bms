#!/usr/bin/env bash

set -euxo pipefail

BASEDIR=$(dirname "$0")
cp ${BASEDIR}/_crypt.php ${BASEDIR}/crypt.php
cp ${BASEDIR}/_password.php ${BASEDIR}/password.php
cp ${BASEDIR}/_salt.php ${BASEDIR}/salt.php
