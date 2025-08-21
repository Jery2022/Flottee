# 🚗 Gestion de Flotte de Voitures d'Entreprise

Ce projet est une application web permettant de gérer une flotte de voitures d'entreprise. Il est développé en **PHP** pour le backend avec une base de données **MySQL**, et dispose d'une interface **front-end** en **HTML/CSS/JavaScript** pour interagir avec les données.

L'application permet :

- L'authentification sécurisée des utilisateurs (admin/employé)
- La gestion des véhicules (ajout, modification, suppression, consultation)
- La gestion des utilisateurs avec rôles et statuts
- Une interface utilisateur interactive avec AJAX
- Une sécurité renforcée (CSRF, SQL Injection, sessions)

Ce projet est conçu pour être facilement déployé sur un serveur local (XAMPP) et peut être étendu pour inclure des fonctionnalités supplémentaires comme la gestion des entretiens, des affectations ou des statistiques de consommation.

## Etape pour le déploiement de Flottee sur Horuku

### Étape 1 : Préparer votre code avec Git

Heroku utilise le système de versionnement Git pour recevoir le code. Nous devons nous assurer que votre projet est un "dépôt" Git et que vos derniers changements sont sauvegardés.

Commandes à taper :

1. Initialisez Git (si ce n'est pas déjà fait) :

   git init

   _(Cette commande crée un sous-dossier caché `.git` qui transforme votre projet en dépôt Git. Si c'est déjà fait, il vous le dira et ce n'est pas grave)._

2. Ajoutez tous vos fichiers pour le "commit" (la sauvegarde) :

   git add .

   _(Le `.` signifie "tous les fichiers et dossiers du projet")._

3. Créez la sauvegarde avec un message descriptif :

   git commit -m "Préparation pour le déploiement sur Heroku"

   Résultat attendu : Après ces commandes, votre code est proprement sauvegardé dans Git, prêt à être envoyé.

---

### Étape 2 : Créer l'application sur Heroku

Cette commande réserve un espace sur les serveurs d'Heroku pour votre application. Elle lui donne un nom unique et une adresse web.

Commande à taper :

heroku create flottee-app-demo-2025v1.0

_(Vous devez choisir un nom unique qui n'est pas déjà pris sur Heroku. S'il est pris, Heroku vous le dira et vous pourrez essayer un autre nom)._

Résultat attendu : Heroku va vous répondre avec l'URL de votre application (ex: `https://flottee-app-demo-2025v1.0.herokuapp.com/`) et l'adresse du dépôt Git Heroku.

### Étape 3 : Ajouter la base de données MySQL

L'application a besoin d'une base de données pour fonctionner. Cette commande demande à Heroku de créer une petite base de données MySQL (via l'add-on ClearDB) et de la lier à votre application.

Commande à taper :

heroku addons:create cleardb:ignite

Résultat attendu : Un message confirmant que la base de données a été créée. En coulisses, Heroku vient de créer la fameuse variable d'environnement `DATABASE_URL` que notre code PHP sait maintenant utiliser.

### Étape 4 : Configurer la clé secrète

Pour des raisons de sécurité, les informations sensibles comme les clés secrètes ne doivent pas être écrites dans le code. On les configure comme des variables d'environnement directement sur Heroku.

Commande à taper :

heroku config:set JWT_SECRET_KEY="ChangezMoiAvecUnePhraseVraimentTresLongueEtSecurisee123!" _(exemple de texte)_

Résultat attendu : Un message confirmant que la variable a été ajoutée à la configuration de l'application.

### Étape 5 : Envoyer le code pour le déploiement

C'est le moment d'envoyer le code (la sauvegarde Git de l'étape 1) vers les serveurs d'Heroku. Heroku va alors lire votre `heroku.yml`, construire l'image Docker et démarrer l'application.

Commande à taper :

git push heroku main

_(Note : Si votre branche principale s'appelle `master`, utilisez `git push heroku master` à la place)._

Résultat attendu : C'est l'étape la plus longue. Vous verrez beaucoup de texte dans votre terminal. Heroku montre en direct la construction du conteneur Docker. Attendez la fin. Le succès est indiqué par un message comme `Verifying deploy... done`.

### Étape 6 : Importer vos données dans la base de données

La base de données que Heroku a créée est vide. Elle n'a ni tables, ni données. Cette commande va lire votre fichier `create_database.sql` et exécuter les commandes SQL qu'il contient sur la nouvelle base de données en ligne.

**Commande à taper :**

heroku db:psql < backend-flottee/sql/create_database.sql

Résultat attendu : La commande s'exécute sans message d'erreur, important ainsi la structure et les données initiales.

Une fois toutes ces étapes terminées, vous pouvez visiter l'URL que Heroku vous a donnée à l'étape 2. Votre application devrait être en ligne !
