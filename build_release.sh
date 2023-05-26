#!/bin/bash

if [ -z "$1" ]; then
    echo "Please provide a version number, like 1.0.0.";
    exit;
else
    echo "Building release version '$1'...";
fi

mkdir -p pirsch
composer install
rm -r p/scripts
cp -r vendor pirsch
cp .htaccess pirsch
cp config.php pirsch
cp -r p pirsch
cp index.php pirsch
zip -r "pirsch_proxy_v$1.zip" pirsch
rm -r pirsch
echo "Done!"
