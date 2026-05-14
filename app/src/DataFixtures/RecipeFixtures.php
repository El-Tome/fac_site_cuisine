<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredients;
use App\Entity\RecipeStep;
use App\Entity\User;
use App\Enum\Difficulty;
use App\Enum\Unit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    private const RECIPE_TITLES = [
        'Poulet rôti aux herbes',
        'Soupe de légumes maison',
        'Pasta carbonara',
        'Salade niçoise',
        'Tarte aux pommes',
        'Ratatouille provençale',
        'Gratin dauphinois',
        'Quiche lorraine',
        'Bœuf bourguignon',
        'Risotto aux champignons',
        'Crêpes sucrées',
        'Moules marinières',
        'Taboulé',
        'Curry de poulet',
        'Saumon en papillote',
    ];

    private const STEP_TITLES = [
        'Préparation des ingrédients',
        'Cuisson',
        'Assaisonnement',
        'Dressage',
        'Finitions',
    ];

    private const INGREDIENT_KEYS = [
        'Carotte', 'Oignon', 'Ail', 'Tomate', 'Courgette',
        'Poulet', 'Bœuf haché', 'Saumon', 'Pâtes', 'Riz',
        'Beurre', 'Crème fraîche', 'Basilic', 'Persil', 'Huile d\'olive',
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $difficulties = Difficulty::cases();
        $units = Unit::cases();

        foreach (self::RECIPE_TITLES as $index => $title) {
            $recipe = new Recipe();
            $recipe->setTitle($title);
            $recipe->setDescription($faker->paragraph(3));
            $recipe->setPrepTime($faker->numberBetween(10, 60));
            $recipe->setCookTime($faker->optional(0.8)->numberBetween(15, 90));
            $recipe->setDifficulty($faker->randomElement($difficulties));
            $recipe->setServings($faker->numberBetween(2, 6));
            $recipe->setFeatured($index < 3);

            $authorRef = 'user_' . $faker->numberBetween(1, 8);
            /** @var User $author */
            $author = $this->getReference($authorRef, User::class);
            $recipe->setAuthor($author);

            $stepCount = $faker->numberBetween(3, 5);
            for ($s = 1; $s <= $stepCount; $s++) {
                $step = new RecipeStep();
                $step->setTitle(self::STEP_TITLES[$s - 1] ?? 'Étape ' . $s);
                $step->setExplanation($faker->paragraph(2));
                $step->setStep($s);
                $recipe->addRecipeStep($step);
            }

            $ingredientKeys = $faker->randomElements(self::INGREDIENT_KEYS, $faker->numberBetween(3, 6));
            foreach ($ingredientKeys as $key) {
                if (!$this->hasReference('ingredient_' . $key, Ingredient::class)) {
                    continue;
                }
                /** @var Ingredient $ingredient */
                $ingredient = $this->getReference('ingredient_' . $key, Ingredient::class);
                $recipeIngredient = new RecipeIngredients();
                $recipeIngredient->setIngredient($ingredient);
                $recipeIngredient->setQuantity($faker->randomFloat(1, 0.5, 500));
                $recipeIngredient->setUnit($faker->randomElement($units));
                $recipe->addRecipeIngredient($recipeIngredient);
            }

            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            IngredientFixtures::class,
        ];
    }
}
