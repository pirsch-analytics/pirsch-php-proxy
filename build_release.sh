#!/bin/bash

if [ -z "$1" ]; then
    echo "Please provide a version number, like 1.0.0.";
    exit;
else
    echo "Building release version '$1'...";
fi

mkdir -p pirsch

npm run build
composer install

cp -r vendor pirsch
cp .htaccess pirsch
cp config.php pirsch
cp session.php pirsch
cp event.php pirsch
cp hit.php pirsch
cp index.php pirsch
cp pirsch-sessions.min.js pirsch
cp pirsch-events.min.js pirsch
cp pirsch.min.js pirsch
cp pa.min.js pirsch
cp proxy.php pirsch

zip -r "pirsch_proxy_v$1.zip" pirsch
rm -r pirsch

echo "Done!"
