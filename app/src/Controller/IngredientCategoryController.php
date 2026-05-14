<?php

namespace App\Controller;

use App\Entity\IngredientCategories;
use App\Form\IngredientCategoryType;
use App\Repository\IngredientCategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/ingredient/category')]
final class IngredientCategoryController extends AbstractController
{
    #[Route('', name: 'app_ingredient_category_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(IngredientCategoriesRepository $repository): Response
    {
        return $this->render('ingredient_category/index.html.twig', [
            'categories' => $repository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_ingredient_category_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $category = new IngredientCategories();
        $form = $this->createForm(IngredientCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie ajoutée avec succès !');

            return $this->redirectToRoute('app_ingredient_category_index');
        }

        return $this->render('ingredient_category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ingredient_category_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function edit(IngredientCategories $category, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(IngredientCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Catégorie modifiée avec succès !');

            return $this->redirectToRoute('app_ingredient_category_index');
        }

        return $this->render('ingredient_category/edit.html.twig', [
            'form' => $form,
            'category' => $category,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_ingredient_category_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(IngredientCategories $category, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-category-' . $category->getId(), $request->request->get('_token'))) {
            $em->remove($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie supprimée.');
        }

        return $this->redirectToRoute('app_ingredient_category_index');
    }
}
