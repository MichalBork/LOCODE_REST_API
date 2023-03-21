#!/bin/sh
composer install
crontab /var/www/symfony/LOCODE_REST_API/cronjob
