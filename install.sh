#!/usr/bin/env bash

cd "$(dirname "$0")"

if [ -d system/storage/cache/view ] ; then
    chmod 0777 system/storage/cache/view
fi
if ! [ -d system/storage/cache/view ] ; then
    mkdir -p -m 0777 system/storage/cache/view
fi
if [ -d system/storage/cache/view ] ; then
    chmod 0777 system/storage/session
fi
if ! [ -d system/storage/session ] ; then
    mkdir -p -m 0777 system/storage/session
fi

if ! [ -f system/configs/config.local.php ] ; then
    cp system/configs/config.sample.php system/configs/config.local.php
    echo "Edit \"system/configs/config.local.php\" to set the ftp server or any other config overrides"
fi

if ! [ -d system/vendor ] ; then
    echo "Install dependencies"
    if ! [ -f composer.phar ] ; then
        curl -s http://getcomposer.org/installer | php
    fi

    php composer.phar selfupdate
    php composer.phar install --no-dev
fi
