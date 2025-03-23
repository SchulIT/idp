#!/bin/sh

CONTAINER_ALREADY_STARTED="SSO_CONTAINER_ALREADY_STARTED"
# Check if the container has already been started
if [ ! -e $CONTAINER_ALREADY_STARTED ]; then
    touch $CONTAINER_ALREADY_STARTED
    echo "-- First container startup --"

    # Clear cache
    php bin/console cache:clear

    # Migrate database
    php bin/console doctrine:migrations:migrate --no-interaction -v

    # Scan for new cronjobs
    php bin/console shapecode:cron:scan

    # Run app setup
    php bin/console app:setup

    # Fetch browscap database
    php bin/console app:browscap:update

    # Fetch GeoIP database (only if MAXMIND_LICENCE_KEY is set)
    if [ -n "${MAXMIND_LICENCE_KEY}" ]; then
        php bin/console geoip2:update
    fi
fi

# Check if the SAML certificate does not exist
if [ ! -f /var/www/html/certs/idp.crt ] || [ ! -f /var/www/html/certs/idp.key ]; then
    echo "Creating SAML certificate..."

    # Create SAML certificate
    php bin/console app:create-certificate --type saml --no-interaction
fi

# Start container
/usr/bin/supervisord -c "/etc/supervisor/conf.d/supervisord.conf"