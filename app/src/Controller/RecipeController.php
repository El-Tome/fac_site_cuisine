<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeLikeRepository;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/recipe')]
final class RecipeController extends AbstractController
{
    #[Route('/new', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em, ImageUploadService $imageUploadService): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setAuthor($this->getUser());
            $this->assignStepNumbers($recipe);

            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $recipe->setImageUrl($imageUploadService->upload($imageFile));
            }

            $em->persist($recipe);
            $em->flush();

            $this->addFlash('success', 'Recette créée avec succès !');

            return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()]);
        }

        return $this->render('recipe/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Recipe $recipe, RecipeLikeRepository $likeRepository): Response
    {
        $isLiked = false;
        if ($this->getUser()) {
            $isLiked = $likeRepository->findOneByUserAndRecipe($this->getUser(), $recipe) !== null;
        }

        return $this->render('recipe/show.html.twig', [
            'recipe'    => $recipe,
            'isLiked'   => $isLiked,
            'likeCount' => $likeRepository->countByRecipe($recipe),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recipe_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em, ImageUploadService $imageUploadService): Response
    {
        if ($recipe->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette recette.');
        }

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->assignStepNumbers($recipe);

            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $recipe->setImageUrl($imageUploadService->upload($imageFile));
            }

            $em->flush();

            $this->addFlash('success', 'Recette modifiée avec succès !');

            return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()]);
        }

        return $this->render('recipe/edit.html.twig', [
            'form' => $form,
            'recipe' => $recipe,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_recipe_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {
        if ($recipe->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette recette.');
        }

        if ($this->isCsrfTokenValid('delete-recipe-' . $recipe->getId(), $request->request->get('_token'))) {
            $em->remove($recipe);
            $em->flush();

            $this->addFlash('success', 'Recette supprimée.');
        }

        return $this->redirectToRoute('app_home_page');
    }

    private function assignStepNumbers(Recipe $recipe): void
    {
        $step = 1;
        foreach ($recipe->getRecipeSteps() as $recipeStep) {
            $recipeStep->setStep($step++);
        }
    }
}
