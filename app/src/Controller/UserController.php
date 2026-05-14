<?php

namespace App\Controller;

use App\Repository\RecipeLikeRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/index.html.twig', [
            'user'    => $this->getUser(),
            'recipes' => $recipeRepository->findBy(['author' => $this->getUser()], ['id' => 'DESC']),
        ]);
    }

    #[Route('/favorites', name: 'app_user_favorites')]
    #[IsGranted('ROLE_USER')]
    public function favorites(RecipeLikeRepository $likeRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user         = $this->getUser();
        $likes        = $likeRepository->findByUserOrderedByRecent($user);
        $likedRecipes = array_map(fn($like) => $like->getRecipe(), $likes);

        return $this->render('user/favorites.html.twig', [
            'likedRecipes' => $likedRecipes,
        ]);
    }
}
