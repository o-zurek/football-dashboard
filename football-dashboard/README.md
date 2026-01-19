# Football Dashboard

Un tableau de bord pour gérer et visualiser les matchs de football.

## Fonctionnalités

- ✅ Affichage des matchs
- ✅ Ajout de nouveaux matchs
- ✅ Import de matchs via CSV
- ✅ Test de connexion à la base de données

## Prérequis

- PHP 7.4+
- MySQL/MariaDB
- XAMPP (ou serveur local similaire)

## Installation

1. Clonez le repository
```bash
git clone https://github.com/VOTRE_USERNAME/football-dashboard.git
```

2. Configurez votre base de données dans `config/database.php`

3. Accédez à l'application via:
```
http://localhost/football-dashboard/
```

## Structure du projet

```
football-dashboard/
├── assets/          # CSS et JavaScript
├── config/          # Configuration (base de données)
├── controllers/     # Logique métier
├── data/            # Fichiers de données CSV
├── models/          # Modèles de données
├── index.php        # Page d'accueil
├── view_matches.php # Affichage des matchs
├── add_match.php    # Ajout de matchs
└── import_csv.php   # Import CSV
```

## Fichiers principaux

- **index.php** - Page d'accueil
- **view_matches.php** - Liste des matchs
- **add_match.php** - Formulaire d'ajout de match
- **import_csv.php** - Import de matchs depuis CSV
- **test_connexion.php** - Test de connexion DB

## Auteur

Votre Nom

## Licence

MIT
