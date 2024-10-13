FROM serversideup/php:8.3-fpm-alpine AS base

# Switch to root to install software
USER root
RUN apk add --update npm nano
RUN install-php-extensions intl

# Switch back to default unprivileged user
USER www-data




# Fix permission issues in development by setting the "www-data"
# user to the same user and group that is running docker.
FROM base AS development
ARG USER_ID
ARG GROUP_ID
RUN docker-php-serversideup-set-id www-data ${USER_ID} ${GROUP_ID}

FROM base AS deploy
COPY --chown=www-data:www-data . /var/www/html


# Set the working directory
WORKDIR /var/www/html
# Install project dependencies
RUN composer install
RUN npm install
# Set up database
RUN touch /var/www/html/database/database.sqlite
RUN php artisan migrate --force
# Running
RUN npm run build
#RUN php artisan serve --host=0.0.0.0 --port=8000
