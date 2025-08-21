@echo off
setlocal enabledelayedexpansion

:: =============================================================================
::  🚀 Script de Lancement pour Flottee App
:: =============================================================================
::
::  Ce script permet de lancer l'application en différents modes :
::  1. Développement Local : Utilise le serveur PHP intégré avec .env.local.
::  2. Production Locale : Simule un environnement de production avec .env.prod.
::  3. Docker : Lance l'application et la base de données avec Docker Compose.
::  4. Docker Stop : Arrête les conteneurs Docker.
::
:: =============================================================================

:menu
cls
echo ==========================================
echo   🚀 Menu de Lancement - Flottee App
echo ==========================================
echo.
echo Choisissez une option :
echo   1 - Lancer en mode Développement (Local)
echo   2 - Lancer en mode Production (Local)
echo   3 - Lancer avec Docker
echo   4 - Arrêter les conteneurs Docker
echo   5 - Quitter
echo.

set /p choice="Entrez votre choix (1-5) : "

if "%choice%"=="1" goto :dev_local
if "%choice%"=="2" goto :prod_local
if "%choice%"=="3" goto :docker_up
if "%choice%"=="4" goto :docker_down
if "%choice%"=="5" goto :eof

echo Choix invalide.
pause
goto :menu

:: --- Mode Développement Local ---
:dev_local
echo.
echo 🧪 Lancement en mode Développement...
cd backend-flottee
if not exist .env.local (
    echo ❌ Fichier .env.local introuvable !
    pause
    goto :menu
)
copy /Y .env.local .env > nul
echo ✅ Fichier .env créé à partir de .env.local.
echo 🌐 Démarrage du serveur PHP sur http://localhost:8000
php -S localhost:8000 -t public
cd ..
goto :eof

:: --- Mode Production Locale ---
:prod_local
echo.
echo 🚀 Lancement en mode Production (simulation locale)...
cd backend-flottee
if not exist .env.prod (
    echo ❌ Fichier .env.prod introuvable !
    pause
    goto :menu
)
copy /Y .env.prod .env > nul
echo ✅ Fichier .env créé à partir de .env.prod.
echo 🌐 Démarrage du serveur PHP sur http://localhost:8080
php -S localhost:8080 -t public
cd ..
goto :eof

:: --- Lancement avec Docker ---
:docker_up
echo.
echo 🐳 Lancement des conteneurs Docker...
docker compose up -d --build
echo.
echo ✅ Conteneurs démarrés. L'application est disponible sur http://localhost:8000
pause
goto :menu

:: --- Arrêt de Docker ---
:docker_down
echo.
echo 🛑 Arrêt des conteneurs Docker...
docker compose down
echo.
echo ✅ Conteneurs arrêtés.
pause
goto :menu
