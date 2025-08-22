@echo off
setlocal enabledelayedexpansion

:: =============================================================================
::  🚀 Script de Lancement pour Flottee App
:: =============================================================================
::
::  Ce script permet de lancer l'application en différents modes :
::  1. Développement (Docker) : Lance les conteneurs avec le code local monté.
::  2. Production (Docker) : Lance les conteneurs avec le code de l'image.
::  3. Arrêter Docker : Arrête tous les conteneurs de l'application.
::
:: =============================================================================

:menu
cls
echo ==========================================
echo   🚀 Menu de Lancement - Flottee App
echo ==========================================
echo.
echo Choisissez une option :
echo   1 - Lancer avec Docker (Developpement)
echo   2 - Lancer avec Docker (Production)
echo   3 - Arreter les conteneurs Docker
echo   4 - Quitter
echo.

set /p choice="Entrez votre choix (1-4) : "

if "%choice%"=="1" goto :docker_up_dev
if "%choice%"=="2" goto :docker_up_prod
if "%choice%"=="3" goto :docker_down
if "%choice%"=="4" goto :eof

echo Choix invalide.
pause
goto :menu

:: --- Lancement avec Docker (Developpement) ---
:docker_up_dev
echo.
echo 🐳 Lancement des conteneurs Docker (Mode Developpement)...
echo    (Le code source local sera utilise)
docker compose up -d --build
echo.
echo ✅ Conteneurs demarres. L'application est disponible sur http://localhost:8000
pause
goto :menu

:: --- Lancement avec Docker (Production) ---
:docker_up_prod
echo.
echo 🚀 Lancement des conteneurs Docker (Mode Production)...
echo    (Le code de l'image Docker sera utilise)
if not exist .env (
    echo ❌ Fichier .env introuvable ! Assurez-vous de l'avoir cree et configure.
    pause
    goto :menu
)
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
echo.
echo ✅ Conteneurs demarres. L'application est disponible sur http://localhost:8000
pause
goto :menu

:: --- Arret de Docker ---
:docker_down
echo.
echo 🛑 Arret des conteneurs Docker...
docker compose down
echo.
echo ✅ Conteneurs arretes.
pause
goto :menu
