#!/usr/bin/env bash

composer install

./vendor/bin/psalm
./vendor/bin/phpstan analyse src

symfony console app:import-results

tail -f /dev/null