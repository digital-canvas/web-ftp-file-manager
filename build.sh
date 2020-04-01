#!/usr/bin/env bash

cd "$(dirname "$0")"

if ! [ -d node_modules ] ; then
    echo "Install npm dependencies"
    yarn
fi

echo "Compile production assets"
./node_modules/.bin/encore production

echo "Install dependencies"
if ! [ -f composer.phar ] ; then
    curl -s http://getcomposer.org/installer | php
fi
php composer.phar selfupdate
php composer.phar install --no-dev

echo "Copy files for distribution"
rsync -rLkEtv --delete \
    --exclude="configs/config.local.php" \
    --exclude="resources/css" \
    --exclude="resources/js" \
    --exclude="storage" \
     ./system/ ./dist/system

if [ -d dist/system/storage/cache/view ] ; then
    chmod 0777 dist/system/storage/cache/view
fi
if ! [ -d dist/system/storage/cache/view ] ; then
    mkdir -p -m 0777 dist/system/storage/cache/view
fi
if [ -d dist/system/storage/cache/view ] ; then
    chmod 0777 dist/system/storage/session
fi
if ! [ -d dist/system/storage/session ] ; then
    mkdir -p -m 0777 dist/system/storage/session
fi

rsync -rLkEtv --delete ./assets/ ./dist/assets
cp .htaccess ./dist/.htaccess
cp index.php ./dist/index.php

echo "Create distribution tarball"
cd dist
if [ -f ftpmanager.tar.gz ] ; then
    rm ftpmanager.tar.gz
fi
if [ -f ftpmanager.zip ] ; then
    rm ftpmanager.zip
fi
zip -r ftpmanager.zip .
tar --exclude=ftpmanager.zip --exclude=ftpmanager.tar.gz -czvf ftpmanager.tar.gz *
