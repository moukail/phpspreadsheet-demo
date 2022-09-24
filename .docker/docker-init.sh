#!/usr/bin/env bash

composer install

symfony check:requirements
symfony check:security

#composer cs-check
#composer cs-fix
composer psalm
composer phpstan

symfony console app:import-results -f Assignment.xlsx

tail -f /dev/null