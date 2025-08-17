@echo off
setlocal enabledelayedexpansion

echo ==========================================
echo   🚀 Lancement de l'application PHP
echo ==========================================
echo.
echo Choisissez un mode :
echo   1 - Développement local (.env.local)
echo   2 - Production (.env.prod)
echo   3 - Docker (utilise .env.prod)
echo.

set /p mode="Entrez le numéro du mode (1/2/3) : "

:: Définir les chemins
set "ENV_LOCAL=.env.local"
set "ENV_PROD=.env.prod"
set "ENV_TARGET=.env"

:: Supprimer l'ancien fichier .env s'il existe
if exist %ENV_TARGET% del %ENV_TARGET%

:: Choix du mode
if "%mode%"=="1" (
    echo 🧪 Mode développement sélectionné
    if exist %ENV_LOCAL% (
        copy /Y %ENV_LOCAL% %ENV_TARGET% >nul
        echo ✅ Fichier .env généré à partir de .env.local
    ) else (
        echo ⚠️ Fichier .env.local introuvable. Création avec valeurs par défaut.
        (
            echo APP_ENV=development
            echo APP_DEBUG=true
            echo DB_HOST=localhost
            echo DB_NAME=flotte_dev
            echo DB_USER=root
            echo DB_PASS=
            echo JWT_SECRET_KEY=dev_secret_key
            echo JWT_ISSUER=http://localhost
            echo JWT_EXPIRATION=3600
            echo DEFAULT_TIMEZONE=Africa/Libreville
            echo LOG_CHANNEL=stack
        ) > %ENV_TARGET%
    )
    goto :launch_local
)

if "%mode%"=="2" (
    echo 🚀 Mode production sélectionné
    if exist %ENV_PROD% (
        copy /Y %ENV_PROD% %ENV_TARGET% >nul
        echo ✅ Fichier .env généré à partir de .env.prod
    ) else (
        echo ⚠️ Fichier .env.prod introuvable. Utilisation de .env.local comme fallback.
        if exist %ENV_LOCAL% (
            copy /Y %ENV_LOCAL% %ENV_TARGET% >nul
        ) else (
            echo ❌ Aucun fichier .env disponible. Abandon.
            goto :eof
        )
    )
    goto :launch_prod
)

if "%mode%"=="3" (
    echo 🐳 Mode Docker sélectionné
    if exist %ENV_PROD% (
        copy /Y %ENV_PROD% %ENV_TARGET% >nul
        echo ✅ Fichier .env généré à partir de .env.prod
    ) else (
        echo ⚠️ Fichier .env.prod introuvable. Utilisation de .env.local comme fallback.
        if exist %ENV_LOCAL% (
            copy /Y %ENV_LOCAL% %ENV_TARGET% >nul
        ) else (
            echo ❌ Aucun fichier .env disponible. Abandon.
            goto :eof
        )
    )
    goto :launch_docker
)

echo ❌ Option invalide. Veuillez relancer le script.
goto :eof

:launch_local
echo 📄 Contenu du fichier .env :
type %ENV_TARGET%
echo.
php -S localhost:8000 -t public
goto :eof

:launch_prod
echo 📄 Contenu du fichier .env :
type %ENV_TARGET%
echo.
php -S 0.0.0.0:8080 -t public
goto :eof

:launch_docker
echo 📄 Contenu du fichier .env :
type %ENV_TARGET%
echo.
docker compose up --build
goto :eof
