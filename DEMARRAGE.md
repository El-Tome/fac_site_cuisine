# Démarrage du projet

Guide complet pour un développeur qui récupère le projet pour la première fois.

---

## Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installé et **démarré**
- `make` disponible dans le terminal
  - macOS : déjà présent, sinon `xcode-select --install`
  - Windows : via [Git Bash](https://gitforwindows.org/) ou WSL
- `git` pour cloner le dépôt

---

## 1. Cloner le projet

```bash
git clone git@github.com:El-Tome/fac_site_cuisine.git
cd fac_site_cuisine
```

---

## 2. Créer le fichier `.env` à la racine

Ce fichier configure Docker Compose (base de données, environnement).  
Un fichier `.env.example` est fourni comme modèle :

```bash
cp .env.example .env
```

Puis ouvrir `.env` et remplir les valeurs :

```dotenv
APP_ENV=dev
APP_SECRET=<une-chaine-aleatoire-de-32-caracteres>

MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=cuisine_projet_web
MYSQL_USER=app
MYSQL_PASSWORD=app

# Ne pas modifier cette ligne — elle est utilisée par le mode Traefik uniquement
DATABASE_URL=mysql://${MYSQL_USER}:${MYSQL_PASSWORD}@mariadb/${MYSQL_DATABASE}
```

> **APP_SECRET** : cette valeur doit être une chaîne hexadécimale de 32 caractères.  
> Pour en générer une :
> ```bash
> openssl rand -hex 16
> ```
> Coller le résultat dans `APP_SECRET=`.

> **MYSQL_*** : ces valeurs sont libres pour un environnement local. Les valeurs ci-dessus sont des exemples fonctionnels.

---

## 3. Créer le fichier `app/.env`

Symfony a besoin d'un fichier `.env` dans le dossier `app/` pour démarrer.  
Ce fichier est ignoré par git — il faut le créer à partir du modèle :

```bash
cp app/.env.example app/.env
```

> Les valeurs peuvent rester vides : Docker injecte `APP_ENV`, `APP_SECRET` et `DATABASE_URL` directement dans le container via le `.env` racine.

---

## 4. Lancer les containers

```bash
make up
```

Cette commande :
1. Démarre les containers Docker (PHP, Nginx, MariaDB, phpMyAdmin)
2. Lance automatiquement `composer install`

Attendre que la commande se termine. La base de données met quelques secondes à être prête.

---

## 5. Initialiser la base de données

```bash
make migrate
```

Applique toutes les migrations Doctrine pour créer les tables.

---

## 6. (Optionnel) Charger des données de test

```bash
make fixtures
```

> **Attention** : cette commande **purge toute la base** avant de recharger les données.

Données générées :

| Données | Quantité |
|---------|----------|
| Catégories d'ingrédients | 10 |
| Ingrédients | ~70 |
| Utilisateurs | 10 |
| Recettes | 15 |

Comptes créés :

| Email | Mot de passe | Rôle |
|-------|-------------|------|
| `admin@cuisine.fr` | `password` | Admin |
| `editor@cuisine.fr` | `password` | Éditeur |
| *(8 emails aléatoires)* | `password` | Utilisateur |

---

## 7. Accéder à l'application

| Service | URL |
|---------|-----|
| Application | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |

---

## 8. Arrêter le projet

```bash
make down
```

---

## Récap des commandes utiles

| Commande | Description |
|----------|-------------|
| `make up` | Démarrer les containers + composer install |
| `make down` | Arrêter les containers |
| `make logs` | Voir les logs en temps réel |
| `make shell` | Ouvrir un shell dans le container PHP |
| `make migrate` | Appliquer les migrations |
| `make fixtures` | Purger la BDD et générer des données de test |
| `make cc` | Vider le cache Symfony |
| `make migration` | Générer une nouvelle migration |

---

## Problèmes fréquents

**`Fatal error: Unable to read the "/var/www/app/.env"` pendant `make up`**  
→ Le fichier `app/.env` n'existe pas. Exécuter `cp app/.env.example app/.env` puis relancer `make up`.

**`make up` échoue sur `composer install`**  
→ Vérifier que Docker Desktop est bien démarré.

**Erreur de connexion à la base de données au premier `make up`**  
→ MariaDB met quelques secondes à démarrer. Relancer `make migrate` après quelques instants.

**Port 8080 ou 8081 déjà utilisé**  
→ Modifier les ports dans `compose.simple.yml` (`"8080:80"` → `"8090:80"` par exemple).

**`APP_SECRET` vide — erreur Symfony**  
→ S'assurer que `APP_SECRET` est bien renseigné dans le `.env` à la racine (voir étape 2).