# =============================================================================
# Stage 1 — Node: compile front-end assets
# =============================================================================
FROM registry.access.redhat.com/ubi9/nodejs-22:latest AS node-builder

# UBI images run as non-root (1001) by default; switch to root so we can
# create /app with the correct ownership, then drop back to 1001.
USER root
RUN mkdir -p /app && chown -R 1001:0 /app && chmod -R g=u /app
USER 1001

WORKDIR /app

COPY --chown=1001:0 package*.json ./
RUN npm ci --ignore-scripts

COPY --chown=1001:0 vite.config.js ./
COPY --chown=1001:0 resources/ resources/

RUN npm run build

# =============================================================================
# Stage 2 — PHP: install Composer dependencies
# =============================================================================
FROM registry.access.redhat.com/ubi9/php-82:latest AS composer-builder

USER root
RUN mkdir -p /app && chown -R 1001:0 /app && chmod -R g=u /app

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

USER 1001
WORKDIR /app

COPY --chown=1001:0 composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

COPY --chown=1001:0 . .
RUN mkdir -p bootstrap/cache storage/framework/{cache,sessions,views} storage/logs \
    && composer dump-autoload --no-dev --optimize --classmap-authoritative

# =============================================================================
# Stage 3 — Final runtime image
# =============================================================================
FROM registry.access.redhat.com/ubi9/php-82:latest

LABEL org.opencontainers.image.title="Red Hat Products" \
      org.opencontainers.image.description="Laravel CRUD application for Red Hat products" \
      org.opencontainers.image.base.name="registry.access.redhat.com/ubi9/php-82"

# Install pdo_pgsql and other required PHP extensions
USER root
RUN dnf install -y --setopt=tsflags=nodocs \
        php-pdo \
        php-pgsql \
        php-opcache \
        php-mbstring \
        php-xml \
        php-tokenizer \
        php-bcmath \
        php-pcntl \
    && dnf clean all \
    && rm -rf /var/cache/dnf \
    && mkdir -p /var/www/html \
    && chown -R 1001:0 /var/www/html \
    && chmod -R g=u /var/www/html

WORKDIR /var/www/html

# Copy application code from builder stages
COPY --from=composer-builder --chown=1001:0 /app /var/www/html
COPY --from=node-builder      --chown=1001:0 /app/public/build /var/www/html/public/build

# Ensure Laravel writable directories exist with correct permissions
RUN mkdir -p storage/framework/{cache,sessions,views} \
             storage/app/public \
             storage/logs \
             bootstrap/cache \
    && chown -R 1001:0 storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Switch to non-root user (UBI default app user)
USER 1001

# Optimise the Laravel config/route/view caches at build time.
# APP_KEY is supplied via --env at build time or injected at runtime.
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=warning

# Expose PHP-FPM port (pair with nginx/httpd sidecar) — or use built-in server for simplicity
EXPOSE 8000

# Entrypoint: run pending migrations (--seed --force) then start the built-in server
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan migrate --seed --force && \
    php artisan serve --host=0.0.0.0 --port=8000
