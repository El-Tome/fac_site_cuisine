<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findRandom(10);
        $hero    = array_shift($recipes);

        return $this->render('home_page/index.html.twig', [
            'hero'    => $hero,
            'recipes' => $recipes,
        ]);
    }
}
