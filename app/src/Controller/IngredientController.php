<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/ingredient')]
#[IsGranted('ROLE_USER')]
final class IngredientController extends AbstractController
{
    #[Route('', name: 'app_ingredient_index', methods: ['GET'])]
    public function index(IngredientRepository $repository): Response
    {
        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $repository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_ingredient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ingredient);
            $em->flush();

            $this->addFlash('success', 'Ingrédient ajouté avec succès !');

            return $this->redirectToRoute('app_ingredient_index');
        }

        return $this->render('ingredient/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ingredient_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Ingrédient modifié avec succès !');

            return $this->redirectToRoute('app_ingredient_index');
        }

        return $this->render('ingredient/edit.html.twig', [
            'form' => $form,
            'ingredient' => $ingredient,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_ingredient_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Ingredient $ingredient, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-ingredient-' . $ingredient->getId(), $request->request->get('_token'))) {
            $em->remove($ingredient);
            $em->flush();

            $this->addFlash('success', 'Ingrédient supprimé.');
        }

        return $this->redirectToRoute('app_ingredient_index');
    }
}
