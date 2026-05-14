<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeLike;
use App\Repository\RecipeLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RecipeLikeController extends AbstractController
{
    #[Route('/recipe/{id}/like', name: 'app_recipe_like', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function toggle(
        Recipe $recipe,
        Request $request,
        RecipeLikeRepository $likeRepository,
        EntityManagerInterface $em,
    ): JsonResponse {
        if (!$this->isCsrfTokenValid('like-recipe-' . $recipe->getId(), $request->request->get('_token'))) {
            return $this->json(['error' => 'Invalid CSRF token'], 403);
        }

        $user     = $this->getUser();
        $existing = $likeRepository->findOneByUserAndRecipe($user, $recipe);

        if ($existing) {
            $em->remove($existing);
            $liked = false;
        } else {
            $like = new RecipeLike();
            $like->setUser($user);
            $like->setRecipe($recipe);
            $em->persist($like);
            $liked = true;
        }

        $em->flush();

        return $this->json([
            'liked' => $liked,
            'count' => $likeRepository->countByRecipe($recipe),
        ]);
    }
}
