FROM php:8.1.18-cli-alpine

ARG user=www-data
ARG uid=1000

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    shadow # For usermod

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN ((getent group $user && groupmod -g $uid $user) || addgroup -g $uid -S $user) && \
    ((getent passwd $user && usermod -u $uid $user) || adduser -u $uid -S $user -G $user)

# Set working directory
RUN mkdir -p /var/www && chown -R $user:$user /var/www
WORKDIR /var/www

USER $user

# Clear cache
RUN rm -rf /tmp/*
