# GTI619 LAB 5 - Contrôle d'accès basé sur les rôles (RBAC)

Ce projet implémente un contrôle d'accès basé sur les rôles (RBAC) dans une application Laravel. Les utilisateurs se voient attribuer des rôles spécifiques pour accéder à certaines fonctionnalités :

- **Admin** : Accès à `/admin/settings` et `/client/business`.
- **Préposé aux clients résidentiels** : Accès à `/client/residential`.


## Prérequis
- **PHP** (8.0+)
- **Composer**
- **SQLite**


## Instructions de configuration

### 1. Installer les dépendances
Exécutez la commande suivante pour installer les dépendances PHP :
```
bash
composer install
```

### 2. Créer la base de données SQLite
Accédez au dossier database et créez une base de données SQLite :

```
cd database
touch database.sqlite
```

### 3. Exécuter les migrations
Accédez au dossier database et créez une base de données SQLite :

```
php artisan migrate
```

### 4. Lancer le serveur de développement
Accédez au dossier database et créez une base de données SQLite :

```
php artisan serve
```

## Accéder à l'application
- Page d'accueil : `http://127.0.0.1:8000/`
- Paramètres Admin : `http://127.0.0.1:8000/admin/settings`
- Clients résidentiels : `http://127.0.0.1:8000/client/residential`
- Clients commerciaux : `http://127.0.0.1:8000/client/business`

## Conseils de dépannage
1. Pages vides : Vérifiez que la base de données contient des données de test ou que les vues contiennent des espaces réservés (placeholders).
2. Effacement du cache : Si les routes ou les configurations échouent, effacez le cache avec :
```
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

## Aperçu de la structure des fichiers
- Contrôleurs
    - `AdminController.php` : Gère les paramètres administratifs.
    - `ClientController.php` : Gère les pages des clients.
- Middleware
    - `CheckRole.php` : Applique le contrôle d'accès basé sur les rôles.
- Vues
    - `master.blade.php` : Mise en page de base.
    - `client/residential.blade.php` : Vue des clients résidentiels.
    - `client/business.blade.php` : Vue des clients commerciaux.
    - `admin/settings.blade.php` : Vue des paramètres admin.
- Routes
    - Définies dans `routes/web.php`.

## Conclusion
Cette application illustre le contrôle d'accès basé sur les rôles. Utilisez les rôles appropriés pour tester l'application. Pour plus de détails sur la configuration ou des explications supplémentaires, veuillez consulter la documentation complète du projet.





