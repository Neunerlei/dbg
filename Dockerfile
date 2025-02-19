
# ===============================
# app root
# ===============================
# syntax = docker/dockerfile:1.2
FROM neunerlei/php:8.4-fpm-alpine as app_root
ARG APP_ENV=prod
ENV APP_ENV=${APP_ENV}
# @see https://aschmelyun.com/blog/fixing-permissions-issues-with-docker-compose-and-php/
ARG DOCKER_RUNTIME=docker
ARG DOCKER_GID=1000
ARG DOCKER_UID=1000

# ===============================
# app dev
# ===============================
FROM app_root AS app_dev

ENV DOCKER_RUNTIME=${DOCKER_RUNTIME:-docker}
ENV APP_ENV=dev

# Add sudo command
RUN --mount=type=cache,id=apk-cache,target=/var/cache/apk rm -rf /etc/apk/cache && ln -s /var/cache/apk /etc/apk/cache && \
    apk update && apk upgrade && apk add \
	  sudo

# Add Composer
COPY --from=index.docker.io/library/composer:latest /usr/bin/composer /usr/bin/composer

# Because we inherit from the prod image, we don't actually want the prod settings
COPY docker/php/config/php.dev.ini /usr/local/etc/php/conf.d/zzz.app.dev.ini
RUN rm -rf /usr/local/etc/php/conf.d/zzz.app.prod.ini

# Recreate the www-data user and group with the current users id
RUN --mount=type=cache,id=apk-cache,target=/var/cache/apk rm -rf /etc/apk/cache && ln -s /var/cache/apk /etc/apk/cache && \
	apk update && apk upgrade && apk add shadow \
       && (userdel -r www-data || true) \
       && (groupdel -f www-data || true) \
       && groupadd -g ${DOCKER_GID} www-data \
       && adduser -u ${DOCKER_UID} -D -S -G www-data www-data

USER www-data
