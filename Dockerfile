### --- First Stage: Base Image --- ###

# Build custom FrankenPHP image
FROM dunglas/frankenphp:builder AS builder

COPY --from=caddy:builder /usr/bin/xcaddy /usr/bin/xcaddy

RUN CGO_ENABLED=1 \
    XCADDY_SETCAP=1 \
    XCADDY_GO_BUILD_FLAGS="-ldflags='-w -s' -tags=nobadger,nomysql,nopgx" \
    CGO_CFLAGS=$(php-config --includes) \
    CGO_LDFLAGS="$(php-config --ldflags) $(php-config --libs)" \
    xcaddy build \
        --output /usr/local/bin/frankenphp \
        --with github.com/dunglas/frankenphp=./ \
        --with github.com/dunglas/frankenphp/caddy=./caddy/ \
        --with github.com/dunglas/caddy-cbrotli \
        # Add extra Caddy modules here \
        --with github.com/baldinof/caddy-supervisor

RUN install-php-extensions \
    @composer \
    pdo_mysql \
    pcntl  \
    intl  \
    zip  \
    xsl  \
    imagick \
    apcu \
    sysvsem \
    pcntl \
    opcache

# Copy shared libs of frankenphp and all installed extensions to temporary location
# You can also do this step manually by analyzing ldd output of frankenphp binary and each extension .so file
RUN <<-EOF
	apt-get update
	apt-get install -y --no-install-recommends libtree
	mkdir -p /tmp/libs
	for target in $(which frankenphp) \
		$(find "$(php -r 'echo ini_get("extension_dir");')" -maxdepth 2 -name "*.so"); do
		libtree -pv "$target" 2>/dev/null | grep -oP '(?:── )\K/\S+(?= \[)' | while IFS= read -r lib; do
			[ -f "$lib" ] && cp -n "$lib" /tmp/libs/
		done
	done
EOF

FROM dunglas/frankenphp AS runner

LABEL maintainer="SchulIT" \
      description="Single Sign-On - SAML-Identity Provider für SchulIT Anwendungen"

# Install dependencies and PHP extensions
COPY --from=builder /usr/local/bin/frankenphp /usr/local/bin/frankenphp
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /tmp/libs /usr/lib
COPY --from=builder /usr/local/bin/composer /usr/bin/composer

COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=builder /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Set DB version so that symfony does not try to connect to a real DB
ENV DATABASE_SERVER_VERSION=10.11.0-MariaDB

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /app

# Copy whole project into image
COPY . .

# Run composer install
RUN composer install --no-dev --classmap-authoritative --no-scripts

# Install assets (copy 3rd party stuff)
RUN php bin/console assets:install

# Install JS/CSS assets
RUN php bin/console importmap:install

# Compile assets
RUN php bin/console asset-map:compile

# Copy startup.sh
COPY .docker/startup.sh startup.sh
RUN chmod +x startup.sh

# Copy Caddyfile
COPY .docker/Caddyfile /etc/caddy/Caddyfile

# Export HTTP port
EXPOSE 80

HEALTHCHECK --start-period=60s CMD php -r 'exit(false === @file_get_contents("http://localhost:2019/metrics", context: stream_context_create(["http" => ["timeout" => 5]])) ? 1 : 0);'
CMD ["./startup.sh"]