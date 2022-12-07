#!/usr/bin/env bash

cp .env.develop .env

composer update

symfony check:requirements
symfony check:security

#composer cs-check
#composer cs-fix
composer psalm
composer phpstan

symfony console app:import-results -f Assignment.xlsx -t console

tail -f /dev/null