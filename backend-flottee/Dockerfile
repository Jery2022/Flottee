# Utiliser une image PHP officielle avec Apache
FROM php:8.2-apache

# Définir le répertoire de travail dans le conteneur
WORKDIR /var/www/html

# Installer les dépendances système nécessaires pour les extensions PHP
# et pour Composer.
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP requises par votre application
RUN docker-php-ext-install pdo pdo_mysql zip

# Installer Composer (le gestionnaire de dépendances pour PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de définition des dépendances
COPY composer.json composer.lock ./

# Installer les dépendances du projet avec Composer
# --no-interaction: Ne pose pas de questions interactives
# --optimize-autoloader: Optimise l'autoloader pour la production
RUN composer install --no-interaction --optimize-autoloader

# Copier le reste des fichiers de l'application
COPY . .

# Copier le fichier de configuration d'Apache personnalisé pour définir le DocumentRoot
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Activer le module de réécriture d'URL d'Apache pour les "jolies" URL (pretty URLs)
RUN a2enmod rewrite

# Changer le propriétaire des fichiers pour l'utilisateur Apache
# afin d'éviter les problèmes de permissions
RUN chown -R www-data:www-data /var/www/html

# Le DocumentRoot d'Apache est déjà /var/www/html, mais votre application
# semble utiliser /var/www/html/public. Nous allons ajuster la configuration d'Apache.
# Pour simplifier, nous allons laisser le WORKDIR sur /var/www/html et
# Apache pointera vers le dossier public via la configuration.
# Une meilleure approche serait d'avoir un virtual host, mais pour commencer,
# le .htaccess dans le dossier public devrait suffire si bien configuré.

# Le port 80 est déjà exposé par l'image de base php:8.2-apache
