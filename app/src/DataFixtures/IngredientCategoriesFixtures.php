<?php

namespace App\DataFixtures;

use App\Entity\IngredientCategories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientCategoriesFixtures extends Fixture
{
    public const CATEGORIES = [
        'Légumes',
        'Fruits',
        'Viandes',
        'Poissons & Fruits de mer',
        'Produits laitiers',
        'Céréales & Féculents',
        'Légumineuses',
        'Épices & Herbes',
        'Condiments & Sauces',
        'Matières grasses',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $name) {
            $category = new IngredientCategories();
            $category->setName($name);
            $manager->persist($category);
            $this->addReference('ingredient_category_' . $name, $category);
        }

        $manager->flush();
    }
}
