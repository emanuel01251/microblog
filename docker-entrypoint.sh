#!/bin/bash

# Always run composer install on container start
composer install --no-interaction --no-progress

# Start Apache
exec "$@"