# Récap des fonctionnalités et pages

Application de cuisine en Symfony 7.4 — PHP 8.5 — MariaDB

---

## Pages publiques (sans connexion)

| URL | Description |
|-----|-------------|
| `http://localhost:8080/` | Page d'accueil — hero, recettes en vedette, plats de la semaine |
| `http://localhost:8080/recipe/{id}` | Détail d'une recette (titre, description, étapes, ingrédients, likes) |
| `http://localhost:8080/login` | Connexion |
| `http://localhost:8080/register` | Inscription |

---

## Pages utilisateur connecté (`ROLE_USER`)

| URL | Description |
|-----|-------------|
| `http://localhost:8080/profile` | Profil utilisateur |
| `http://localhost:8080/user/edit` | Modifier son profil (prénom, nom, pseudo) |
| `http://localhost:8080/user/delete` | Supprimer son compte |
| `http://localhost:8080/favorites` | Recettes likées / favorites |
| `http://localhost:8080/meal-plan` | Planificateur de repas — génère un plan aléatoire |
| `http://localhost:8080/meal-plan/saved` | Plans de repas sauvegardés |
| `http://localhost:8080/recipe/{id}` (like) | Bouton like sur une recette (POST) |
| `http://localhost:8080/recipe/new` | Créer une recette (avec étapes et ingrédients) |
| `http://localhost:8080/recipe/{id}/edit` | Modifier une recette |
| `http://localhost:8080/recipe/{id}/delete` | Supprimer une recette |

---

## Pages éditeur (`ROLE_EDITOR` — hérite de `ROLE_USER`)

| URL | Description |
|-----|-------------|
| `http://localhost:8080/ingredient` | Liste des ingrédients |
| `http://localhost:8080/ingredient/new` | Créer un ingrédient |
| `http://localhost:8080/ingredient/{id}/edit` | Modifier un ingrédient |
| `http://localhost:8080/ingredient/category` | Liste des catégories d'ingrédients |
| `http://localhost:8080/ingredient/category/new` | Créer une catégorie |
| `http://localhost:8080/ingredient/category/{id}/edit` | Modifier une catégorie |

---

## Pages admin (`ROLE_ADMIN` — hérite de `ROLE_EDITOR`)

| URL | Description |
|-----|-------------|
| `http://localhost:8080/admin` | Dashboard admin — gestion globale |
| `http://localhost:8080/admin/user/{id}/promote` | Promouvoir un utilisateur (→ editor ou admin) |
| `http://localhost:8080/admin/user/{id}/demote` | Rétrograder un utilisateur |
| `http://localhost:8080/admin/user/{id}/delete` | Supprimer un utilisateur |
| `http://localhost:8080/admin/recipe/{id}/feature` | Mettre/retirer une recette en vedette |
| `http://localhost:8080/admin/recipe/{id}/delete` | Supprimer une recette |
| `http://localhost:8080/admin/ingredient/{id}/delete` | Supprimer un ingrédient |
| `http://localhost:8080/admin/ingredient-category/{id}/delete` | Supprimer une catégorie d'ingrédient |
| `http://localhost:8080/ingredient/{id}/delete` | Supprimer un ingrédient |
| `http://localhost:8080/ingredient/category/{id}/delete` | Supprimer une catégorie |

---

## API interne (utilisée par le front JS)

| URL | Description |
|-----|-------------|
| `GET /api/ingredient/search?q=...` | Recherche d'ingrédients par nom |
| `GET /api/ingredient/categories` | Liste des catégories d'ingrédients |
| `POST /api/ingredient/category` | Créer une catégorie via API |
| `POST /api/ingredient` | Créer un ingrédient via API |

---

## Hiérarchie des rôles

```
ROLE_ADMIN
  └── ROLE_EDITOR
        └── ROLE_USER
```

---

## Entités principales

| Entité | Description |
|--------|-------------|
| `User` | Compte utilisateur (email, pseudo, prénom, nom, rôles) |
| `Recipe` | Recette (titre, description, durées, difficulté, portions, image, vedette) |
| `RecipeStep` | Étape d'une recette (numéro, titre, explication) |
| `RecipeIngredients` | Lien recette ↔ ingrédient (quantité, unité) |
| `RecipeLike` | Like d'un utilisateur sur une recette |
| `Ingredient` | Ingrédient (nom, catégories) |
| `IngredientCategories` | Catégorie d'ingrédient (Légumes, Viandes…) |
| `MealPlan` | Plan de repas sauvegardé par un utilisateur |
