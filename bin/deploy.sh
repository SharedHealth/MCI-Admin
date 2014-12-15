#!/bin/sh

mkdir -p app/cache
mkdir -p app/logs

echo "Removing old cache if any"
rm -rf app/cache/*
rm -rf app/logs/*

echo "Generating bootstrap cache"
php vendor/sensio/distribution-bundle/Sensio/Bundle/DistributionBundle/Resources/bin/build_bootstrap.php

echo "Creating assets symlink"
ln -s ../app/Resources/assets/ web/assets

echo "Dumping assets"
app/console  assets:install --symlink --relative --env=prod --no-debug
app/console  assetic:dump --env=prod --no-debug

echo "Make directory writtable"
chmod -R 0777 app/cache app/logs