# Démarrage du projet (version simple, sans Traefik)

## Prérequis

- Docker Desktop installé et démarré
- `make` disponible dans le terminal

---

## 1. Configurer le mode (Makefile)

En haut du `Makefile`, une variable contrôle le mode :

```makefile
WITH_TRAEFIK = false   # sans Traefik (défaut) → http://localhost:8080
WITH_TRAEFIK = true    # avec Traefik → domaine configuré
```

---

## 2. Lancer le projet

```bash
make up
```

L'application est accessible sur (en mode `false`) :
- **App** → http://localhost:8080
- **phpMyAdmin** → http://localhost:8081

---

## 3. Premier lancement — initialiser la base de données

```bash
make migrate
```

---

## 4. Générer des données aléatoires (fixtures)

```bash
make fixtures
```

> **Attention** : cette commande purge toute la base de données avant de recharger les données.

### Ce que les fixtures génèrent :

| Données | Quantité |
|---------|----------|
| Catégories d'ingrédients | 10 (fixes) |
| Ingrédients | ~70 (fixes, réalistes) |
| Utilisateurs | 10 (2 comptes fixes + 8 aléatoires) |
| Recettes | 15 (avec étapes et ingrédients) |

### Comptes créés automatiquement :

| Email | Mot de passe | Rôle |
|-------|-------------|------|
| `admin@cuisine.fr` | `password` | Admin |
| `editor@cuisine.fr` | `password` | Éditeur |
| *(8 emails aléatoires)* | `password` | Utilisateur |

---

## 5. Arrêter le projet

```bash
make down
```

---

## Récap des commandes utiles

| Commande | Description |
|----------|-------------|
| `make up` | Démarrer les containers |
| `make down` | Arrêter les containers |
| `make logs` | Voir les logs en temps réel |
| `make shell` | Ouvrir un shell dans le container PHP |
| `make fixtures` | Purger la BDD et générer des données aléatoires |
| `make migrate` | Appliquer les migrations |
| `make cc` | Vider le cache Symfony |
