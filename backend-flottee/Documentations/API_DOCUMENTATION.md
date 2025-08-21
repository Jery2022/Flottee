# API Documentation – Gestion de Flotte de Voitures

Ce document décrit l'API backend PHP pour la gestion d'une flotte de voitures d'entreprise. L'API est construite en PHP 8.2 avec une base de données MySQL et utilise des routes REST sécurisées.

## Description Générale

L'API permet de gérer les utilisateurs, les véhicules, les contrats, les fournisseurs, les clients, les paiements, les factures, les garages d'entretien, les employés, les stocks de pièces, les produits d'entretien, les achats. Elle est conçue pour être utilisée avec une interface front-end développée en Javascript vanilla/voir React plus tard et communique via des requêtes HTTP (GET, POST, PUT, DELETE).

L'authentification est assurée par un système de jetons JWT, et les accès sont contrôlés selon les rôles des utilisateurs (admin, employé). Les données sont échangées au format JSON et les communications sont sécurisées par des en-têtes CORS et des validations côté serveur.

Chaque ressource dispose de son propre contrôleur, model et routeur, permettant une organisation claire et modulaire du code. Les interactions avec la base de données sont réalisées via PDO avec des requêtes préparées pour éviter les injections SQL.

## Accès aux l’API

On peux appeler mes APIs REST comme ceci :

    GET /api/routes/auth → fournit un token valide

### Pour les utilisateurs

    GET /api/routes/users → liste des utilisateurs

    GET /api/routes/users/3 → liste un utilisateur spécifique


    POST /api/routes/user → créer un utilisateur

    PUT /api/routes/users/9 → modifier un utilisateur

    DELETE /api/routes/users/8 → supprimer un utilisateur

    GET /api/routes/users/9/restore → Restore un utilisateur spécifique

### Pour les voitures

    GET /api/routes/vehicles → liste des véhicules

    GET /api/routes/vehicles/5 → liste un véhicule spécifique

    POST /api/routes/vehicles → créer un véhicule

    PUT /api/routes/vehicles/5 → modifier un véhicule

    DELETE /api/routes/vehicles/6 → supprimer un véhicules

### Test Route API REST

#### Route : GET /api/routes/auth

Données saisies :
{
"email":"alice@example.com",
"password":"1234Test"
}

Resultat :
{
"status": "success",
"token": "MyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.......
"redirect": "admin_dashboard.php",
"user": {
"id": 1,
"email": "alice@example.com",
"name": null,
"status": "active"
}
}

#### Route : GET /api/routes/users

Données saisies :
Aucune.

Resultat :
[
{
"id": 1,
"first_name": "Alice",
"last_name": "Ngoma",
"pseudo": "alice01",
"email": "alice@example.com",
"password": "$2y$10$N7F6ZlFnYwE5gxAmzsakbOX00LTGnlMuY6Cq1y9kwVvFzQ6PM2voq",
"role": "admin",
"status": "active",
"last_login": "2025-08-01 10:00:00",
"created_at": "2025-08-12 13:01:16",
"deleted_at": null
},
{
"id": 2,
"first_name": "Bruno",
"last_name": "Mouele",
"pseudo": "bruno02",
"email": "bruno@example.com",
"password": "hashed_pwd_2",
"role": "user",
"status": "active",
"last_login": "2025-08-02 09:30:00",
"created_at": "2025-08-12 13:01:16",
"deleted_at": null
},
.....
]

#### Route : POST /api/routes/users/{id}/restore

Données saisies :
Resultat :
{
"success": true,
"message": "Utilisateur restauré avec succès",
"data": {
"id": 9,
"restored_at": "2025-08-18 23:45:00"
}
}

#### Route : POST /api/routes/user

Données saisies :
{
"pseudo": "Jean",
"first_name": "Jean",
"last_name": "Dupont",
"email": "jean.dupont@example.com",
"password": "motdepasse123",
"role": "user"
}
Resultat :
{
"success": true,
"message": "Utilisateur créé avec succès",
"data": {
"id": 11,
"first_name": "Jean",
"last_name": "Dupont",
"pseudo": "Jean",
"email": "jean.dupont@example.com",
"password": "$2y$10$ixIV7wcV0L6zFvqTZI0pjeVa1fjsMoAQJjqe3AZ2gsjjjw4lQbkoq",
"role": "user",
"status": "active",
"last_login": null,
"created_at": "2025-08-19 00:15:12",
"deleted_at": null
}
}

#### Route : GET /api/routes/users/3

Données saisies :
Aucune.

Resultat :
{
"id": 3,
"first_name": "Chantal",
"last_name": "Obiang",
"pseudo": "chantal03",
"email": "chantal@example.com",
"password": "hashed_pwd_3",
"role": "user",
"status": "inactive",
"last_login": null,
"created_at": "2025-08-12 13:01:16",
"deleted_at": null
}

#### Route : PUT /api/routes/users/9

Données saisies :
{
"first_name": "Putlyse",
"role": "employe"
}
Resultat :
{
"success": true,
"message": "Utilisateur mis à jour avec succès",
"data": {
"id": 9,
"first_name": "Putlyse",
"last_name": "Koumba",
"pseudo": "ines09",
"email": "ines@example.com",
"password": "hashed_pwd_9",
"role": "employe",
"status": "active",
"last_login": "2025-08-07 12:10:00",
"created_at": "2025-08-12 13:01:16",
"deleted_at": null
}
}

#### Route : DELETE /api/routes/users/8

Données saisies :
aucune.

Resultat :
{
"success": true,
"message": "Utilisateur supprimé avec succès",
"data": {
"id": "8",
"deleted_at": "2025-08-19 00:17:44"
}
}

#### Route : GET /api/routes/users/9/restore

Données saisies :
aucune.

Resultat :
{
"success": true,
"message": "Utilisateur restauré avec succès",
"data": {
"id": "9",
"restored_at": "2025-08-19 10:08:22"
}
}
