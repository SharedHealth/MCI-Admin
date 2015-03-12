#!/bin/sh
####################################
#
# Package as tgz.
#
####################################

#DEFINE Some VARIABLES
WORKING_DIR=$(pwd)
varsion="1.0"
ARCHIVE_FILE_NAME="build/mci-ui-$varsion.tgz"

#Download composer
if [ ! -f "composer.phar" ]; then
    echo "Installing composer"
    curl -sS https://getcomposer.org/installer | php
fi

#Download dependencies
SYMFONY_ENV=prod php composer.phar install --no-dev -o -n --no-scripts

mkdir -p build

# Print start status message.
echo "Building package from $WORKING_DIR"

# Backup the files using tar.
tar -zcf ${ARCHIVE_FILE_NAME} -X bin/.exclude ./
# Print end status message.
echo
echo "Build finished"
date
