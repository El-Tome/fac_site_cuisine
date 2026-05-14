<?php

namespace App\Controller;

use App\Entity\MealPlan;
use App\Repository\MealPlanRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/meal-plan')]
final class MealPlanController extends AbstractController
{
    #[Route('', name: 'app_meal_plan', methods: ['GET'])]
    public function index(Request $request, RecipeRepository $recipeRepository): Response
    {
        $count   = $request->query->getInt('count', 0);
        $recipes = [];

        if ($count > 0) {
            $count   = min($count, 20);
            $recipes = $recipeRepository->findRandom($count);
        }

        return $this->render('meal_plan/index.html.twig', [
            'count'   => $count ?: null,
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipe/random', name: 'app_meal_plan_random_recipe', methods: ['GET'])]
    public function randomRecipe(Request $request, RecipeRepository $recipeRepository): Response
    {
        $excludeIds = array_filter(
            array_map('intval', explode(',', $request->query->getString('exclude', ''))),
            fn(int $id) => $id > 0
        );

        $index   = max(1, $request->query->getInt('index', 1));
        $recipes = $recipeRepository->findRandomExcluding($excludeIds, 1);

        if (empty($recipes)) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $this->render('meal_plan/_recipe_item.html.twig', [
            'recipe' => $recipes[0],
            'index'  => $index,
        ]);
    }

    #[Route('/save', name: 'app_meal_plan_save', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function save(Request $request, RecipeRepository $recipeRepository, EntityManagerInterface $em): Response
    {
        $ids = array_filter(
            array_map('intval', $request->request->all('recipe_ids')),
            fn(int $id) => $id > 0
        );

        if (empty($ids)) {
            $this->addFlash('error', 'Aucune recette sélectionnée.');
            return $this->redirectToRoute('app_meal_plan');
        }

        $recipes = $recipeRepository->findBy(['id' => $ids]);

        $plan = new MealPlan();
        $plan->setUser($this->getUser());

        foreach ($recipes as $recipe) {
            $plan->addRecipe($recipe);
        }

        $em->persist($plan);
        $em->flush();

        $this->addFlash('success', 'Sélection sauvegardée !');

        return $this->redirectToRoute('app_meal_plan_saved');
    }

    #[Route('/saved', name: 'app_meal_plan_saved', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function saved(MealPlanRepository $mealPlanRepository): Response
    {
        $plans = $mealPlanRepository->findByUserOrderedByDate($this->getUser());

        return $this->render('meal_plan/saved.html.twig', [
            'plans' => $plans,
        ]);
    }

    #[Route('/saved/{id}/delete', name: 'app_meal_plan_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(MealPlan $plan, Request $request, EntityManagerInterface $em): Response
    {
        if ($plan->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete-meal-plan-' . $plan->getId(), $request->request->get('_token'))) {
            $em->remove($plan);
            $em->flush();
            $this->addFlash('success', 'Sélection supprimée.');
        }

        return $this->redirectToRoute('app_meal_plan_saved');
    }
}
