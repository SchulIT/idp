#!/bin/sh

CONTAINER_ALREADY_STARTED="IDP_CONTAINER_ALREADY_STARTED"
# Check if the container has already been started
if [ ! -e $CONTAINER_ALREADY_STARTED ]; then
    touch $CONTAINER_ALREADY_STARTED
    echo "-- First container startup --"

    # Check if the SAML certificate does not exist
    if [ ! -f /var/www/html/certs/idp.crt ] || [ ! -f /var/www/html/certs/idp.key ]; then
        echo "Creating SAML certificate..."

        # Create SAML certificate
        php bin/console app:create-certificate --type saml --no-interaction
    fi

    # Run database migrations
    php bin/console doctrine:migrations:migrate --no-interaction

    # Perform initial setup
    php bin/console app:setup

    # Register cron jobs
    php bin/console shapecode:cron:scan

    # Update Browscap
    php bin/console app:browscap:update
fi

# Start PHP-FPM
php-fpm &

# Start Nginx
nginx -g 'daemon off;'