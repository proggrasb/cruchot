#!/bin/sh

rm -rf ./var

composer install
composer dump-autoload --optimize --classmap-authoritative
php bin/console doctrine:migrations:migrate -n
