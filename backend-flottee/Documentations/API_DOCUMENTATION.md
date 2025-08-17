# API Documentation – Gestion de Flotte de Voitures

Ce document décrit l'API backend PHP pour la gestion d'une flotte de voitures d'entreprise. L'API est construite en PHP 8.2 avec une base de données MySQL et utilise des routes REST sécurisées.

## Description Générale

L'API permet de gérer les utilisateurs, les véhicules, les contrats, les fournisseurs, les clients, les paiements, les factures, les garages d'entretien, les employés, les stocks de pièces, les produits d'entretien, les achats. Elle est conçue pour être utilisée avec une interface front-end développée en Javascript vanilla/voir React plus tard et communique via des requêtes HTTP (GET, POST, PUT, DELETE).

L'authentification est assurée par un système de jetons JWT, et les accès sont contrôlés selon les rôles des utilisateurs (admin, employé). Les données sont échangées au format JSON et les communications sont sécurisées par des en-têtes CORS et des validations côté serveur.

Chaque ressource dispose de son propre contrôleur, model et routeur, permettant une organisation claire et modulaire du code. Les interactions avec la base de données sont réalisées via PDO avec des requêtes préparées pour éviter les injections SQL.

## Accès aux l’API

On peux appeler mes  APIs REST comme ceci :

### Pour les utilisateurs

    GET /api/routes/users → liste des utilisateurs

    GET /api/routes/users?id=3 → liste un utilisateur spécifique

    POST /api/routes/users → créer un utilisateur

    PUT /api/routes/users?id=3 → modifier un utilisateur

    DELETE /api/routes/users?id=3 → supprimer un utilisateur

### Pour les voitures

    GET /api/routes/vehicles → liste des véhicules

    GET /api/routes/vehicles?id=5 → liste un véhicule spécifique

    POST /api/routes/vehicles → créer un véhicule

    PUT /api/routes/vehicles?id=5 → modifier un véhicule

    DELETE /api/routes/vehicles?id=5 → supprimer un véhicules