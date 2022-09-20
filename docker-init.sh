#!/usr/bin/env bash

composer install

vendor/bin/phpstan analyse src
symfony console

tail -f /dev/null