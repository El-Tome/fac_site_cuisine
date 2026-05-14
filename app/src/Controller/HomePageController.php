<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(Request $request, RecipeRepository $recipeRepository): Response
    {
        $query = trim($request->query->getString('q'));

        $hero    = $recipeRepository->findFeatured();
        $heroId  = $hero?->getId();

        if ($query !== '') {
            $recipes = $recipeRepository->search($query);

            return $this->render('home_page/index.html.twig', [
                'hero'    => $hero,
                'recipes' => array_filter($recipes, fn($r) => $r->getId() !== $heroId),
                'query'   => $query,
            ]);
        }

        $recipes = $recipeRepository->findRandomExcluding($heroId ? [$heroId] : [], 9);

        return $this->render('home_page/index.html.twig', [
            'hero'    => $hero,
            'recipes' => $recipes,
            'query'   => '',
        ]);
    }
}
