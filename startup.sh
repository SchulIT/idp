#!/bin/sh

# Check if the FIRST_RUN environment variable is 1
if [ "$FIRST_RUN" = "1" ]; then
    # Run database migrations
    php bin/console doctrine:migrations:migrate --no-interaction

    # Perform initial setup
    php bin/console app:setup

    # Register cron jobs
    php bin/console shapecode:cron:scan

    # Update Browscap
    php bin/console app:browscap:update

    # Set FIRST_RUN environment variable to 0
    export FIRST_RUN=0
fi

# Start PHP-FPM
php-fpm &

# Start Nginx
nginx -g 'daemon off;'
