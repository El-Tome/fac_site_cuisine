<?php

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use App\Entity\IngredientCategories;
use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\IngredientCategoriesRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    private const LIMIT = 10;

    #[Route('', name: 'app_admin_dashboard', methods: ['GET'])]
    public function dashboard(
        Request $request,
        UserRepository $userRepository,
        RecipeRepository $recipeRepository,
        IngredientRepository $ingredientRepository,
        IngredientCategoriesRepository $ingredientCategoriesRepository,
    ): Response {
        $userSearch       = trim($request->query->get('user_search', ''));
        $recipeSearch     = trim($request->query->get('recipe_search', ''));
        $ingredientSearch = trim($request->query->get('ingredient_search', ''));
        $categorySearch   = trim($request->query->get('category_search', ''));

        $pageUsers       = max(1, (int) $request->query->get('page_users', 1));
        $pageRecipes     = max(1, (int) $request->query->get('page_recipes', 1));
        $pageIngredients = max(1, (int) $request->query->get('page_ingredients', 1));
        $pageCategories  = max(1, (int) $request->query->get('page_categories', 1));

        $totalUsers       = $userRepository->count([]);
        $totalRecipes     = $recipeRepository->count([]);
        $totalIngredients = $ingredientRepository->count([]);
        $totalCategories  = $ingredientCategoriesRepository->count([]);

        return $this->render('admin/index.html.twig', [
            'users'        => $userSearch !== ''
                ? $userRepository->search($userSearch)
                : $userRepository->findBy([], ['id' => 'ASC'], self::LIMIT, ($pageUsers - 1) * self::LIMIT),
            'user_search'  => $userSearch,
            'page_users'   => $pageUsers,
            'total_users'  => $totalUsers,

            'recipes'       => $recipeSearch !== ''
                ? $recipeRepository->search($recipeSearch)
                : $recipeRepository->findBy([], ['id' => 'DESC'], self::LIMIT, ($pageRecipes - 1) * self::LIMIT),
            'recipe_search' => $recipeSearch,
            'page_recipes'  => $pageRecipes,
            'total_recipes' => $totalRecipes,

            'ingredients'       => $ingredientSearch !== ''
                ? $ingredientRepository->search($ingredientSearch)
                : $ingredientRepository->findBy([], ['nom' => 'ASC'], self::LIMIT, ($pageIngredients - 1) * self::LIMIT),
            'ingredient_search' => $ingredientSearch,
            'page_ingredients'  => $pageIngredients,
            'total_ingredients' => $totalIngredients,

            'ingredient_categories' => $categorySearch !== ''
                ? $ingredientCategoriesRepository->search($categorySearch)
                : $ingredientCategoriesRepository->findBy([], ['name' => 'ASC'], self::LIMIT, ($pageCategories - 1) * self::LIMIT),
            'category_search'       => $categorySearch,
            'page_categories'       => $pageCategories,
            'total_categories'      => $totalCategories,

            'limit' => self::LIMIT,
        ]);
    }

    #[Route('/user/{id}/delete', name: 'app_admin_delete_user', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteUser(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Impossible de supprimer votre propre compte.');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        if ($this->isCsrfTokenValid('delete-user-' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé.');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/recipe/{id}/delete', name: 'app_admin_delete_recipe', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteRecipe(Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-recipe-' . $recipe->getId(), $request->request->get('_token'))) {
            $em->remove($recipe);
            $em->flush();
            $this->addFlash('success', 'Recette supprimée.');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/ingredient/{id}/delete', name: 'app_admin_delete_ingredient', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteIngredient(Ingredient $ingredient, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-ingredient-' . $ingredient->getId(), $request->request->get('_token'))) {
            $em->remove($ingredient);
            $em->flush();
            $this->addFlash('success', 'Ingrédient supprimé.');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/ingredient-category/{id}/delete', name: 'app_admin_delete_ingredient_category', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteIngredientCategory(IngredientCategories $category, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-ingredient-category-' . $category->getId(), $request->request->get('_token'))) {
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'Catégorie d\'ingrédient supprimée.');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }
}
