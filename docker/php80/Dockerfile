# Utiliser l'image officielle de PHP 7.3 avec FPM
FROM php:8.0-fpm-buster
ARG TIMEZONE
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY 'docker/php80/php.ini' /usr/local/etc/php/conf.d/docker-php-config.ini

# Mettre à jour le système et installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    && docker-php-ext-install zip \
    && docker-php-ext-install mysqli

RUN ln -snf /usr/share/zoneinfo/$TIMEZONE /etc/localtime && echo $TIMEZONE > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', $TIMEZONE > /usr/local/etc/php/conf.d/tzone.ini \
    && "date"

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier le reste du code de l'application
COPY . /var/www

# Définir le répertoire de travail
WORKDIR /var/www

# Installer les dépendances PHP avec Composer
RUN composer install --no-scripts --no-autoloader

# Installer les dépendances avec autoloader
RUN composer dump-autoload --optimize

# Exposer le port utilisé par PHP-FPM
EXPOSE 9000

# Commande pour lancer PHP-FPM
CMD ["php-fpm"]