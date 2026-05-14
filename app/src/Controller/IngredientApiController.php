<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\IngredientCategories;
use App\Repository\IngredientCategoriesRepository;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/ingredient')]
#[IsGranted('ROLE_USER')]
final class IngredientApiController extends AbstractController
{
    #[Route('/search', name: 'api_ingredient_search', methods: ['GET'])]
    public function search(Request $request, IngredientRepository $repository): JsonResponse
    {
        $q = trim($request->query->get('q', ''));

        if (strlen($q) < 2) {
            return $this->json([]);
        }

        $ingredients = $repository->createQueryBuilder('i')
            ->where('i.nom LIKE :q')
            ->setParameter('q', '%' . $q . '%')
            ->orderBy('i.nom', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $this->json(array_map(
            fn($i) => ['id' => $i->getId(), 'nom' => $i->getNom()],
            $ingredients
        ));
    }

    #[Route('/categories', name: 'api_ingredient_categories', methods: ['GET'])]
    public function categories(IngredientCategoriesRepository $repository): JsonResponse
    {
        $categories = $repository->findBy([], ['name' => 'ASC']);

        return $this->json(array_map(
            fn($c) => ['id' => $c->getId(), 'name' => $c->getName()],
            $categories
        ));
    }

    #[Route('/category', name: 'api_ingredient_category_create', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function createCategory(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $name = trim($data['name'] ?? '');

        if (empty($name)) {
            return $this->json(['error' => 'Le nom est requis.'], 400);
        }

        $category = new IngredientCategories();
        $category->setName($name);
        $em->persist($category);
        $em->flush();

        return $this->json(['id' => $category->getId(), 'name' => $category->getName()], 201);
    }

    #[Route('', name: 'api_ingredient_create', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function create(Request $request, EntityManagerInterface $em, IngredientCategoriesRepository $categoriesRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nom  = trim($data['nom'] ?? '');

        if (empty($nom)) {
            return $this->json(['error' => 'Le nom est requis.'], 400);
        }

        $ingredient = new Ingredient();
        $ingredient->setNom($nom);

        foreach ($data['categoryIds'] ?? [] as $categoryId) {
            $category = $categoriesRepository->find($categoryId);
            if ($category) {
                $ingredient->addCategory($category);
            }
        }

        $em->persist($ingredient);
        $em->flush();

        return $this->json(['id' => $ingredient->getId(), 'nom' => $ingredient->getNom()], 201);
    }
}
