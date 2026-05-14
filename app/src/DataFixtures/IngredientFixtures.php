<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class IngredientFixtures extends Fixture implements DependentFixtureInterface
{
    private const INGREDIENTS = [
        'Légumes'             => ['Carotte', 'Oignon', 'Ail', 'Tomate', 'Courgette', 'Poivron', 'Épinard', 'Brocoli', 'Chou-fleur', 'Poireau'],
        'Fruits'              => ['Citron', 'Orange', 'Pomme', 'Banane', 'Fraise', 'Mangue', 'Avocat', 'Ananas', 'Poire', 'Raisin'],
        'Viandes'             => ['Poulet', 'Bœuf haché', 'Porc', 'Agneau', 'Lardons', 'Canard', 'Dinde'],
        'Poissons & Fruits de mer' => ['Saumon', 'Thon', 'Crevettes', 'Cabillaud', 'Moules', 'Calamars'],
        'Produits laitiers'   => ['Beurre', 'Crème fraîche', 'Lait', 'Gruyère', 'Parmesan', 'Mozzarella', 'Yaourt'],
        'Céréales & Féculents' => ['Riz', 'Pâtes', 'Farine', 'Pain de mie', 'Semoule', 'Quinoa'],
        'Légumineuses'        => ['Lentilles', 'Pois chiches', 'Haricots rouges', 'Fèves'],
        'Épices & Herbes'     => ['Basilic', 'Persil', 'Cumin', 'Paprika', 'Curry', 'Thym', 'Romarin', 'Coriandre'],
        'Condiments & Sauces' => ['Sauce soja', 'Moutarde', 'Ketchup', 'Vinaigre balsamique', 'Huile d\'olive'],
        'Matières grasses'    => ['Huile de tournesol', 'Huile de coco', 'Margarine'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::INGREDIENTS as $categoryName => $ingredients) {
            /** @var \App\Entity\IngredientCategories $category */
            $category = $this->getReference('ingredient_category_' . $categoryName, \App\Entity\IngredientCategories::class);

            foreach ($ingredients as $nom) {
                $ingredient = new Ingredient();
                $ingredient->setNom($nom);
                $ingredient->addCategory($category);
                $manager->persist($ingredient);
                $this->addReference('ingredient_' . $nom, $ingredient);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [IngredientCategoriesFixtures::class];
    }
}
