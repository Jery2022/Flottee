# Utilise une image PHP avec Apache
FROM php:8.5.0alpha4-cli-alpine3.21

# Copie les fichiers de l'application
COPY . /var/www/html/

# Active le module rewrite d'Apache
RUN a2enmod rewrite

# Installe les extensions PHP nécessaires (ex: PDO, mbstring)
RUN docker-php-ext-install pdo pdo_mysql mbstring

# Copie le fichier .env dans le conteneur
COPY .env /var/www/html/.env

# Définit le dossier public comme racine du serveur
WORKDIR /var/www/html/public
