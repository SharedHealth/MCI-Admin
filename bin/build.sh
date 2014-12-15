#!/bin/sh
####################################
#
# Backup as tgz.
# use tar -xzf mci-*.tgz to restore
#
####################################

WORKING_DIR=$(pwd)

# Create archive filename.
varsion="1.0"

mkdir -p build

ARCHIVE_FILE_NAME="build/mci-ui-$varsion.tgz"

# Print start status message.
echo "Building package from $WORKING_DIR"

# Backup the files using tar.
tar -zcf ${ARCHIVE_FILE_NAME} -X bin/.exclude ./
# Print end status message.
echo
echo "Build finished"
date
