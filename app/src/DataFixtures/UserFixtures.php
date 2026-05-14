<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $admin = $this->createUser('admin@cuisine.fr', 'Admin', 'admin', 1, ['ROLE_ADMIN'], $faker);
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        $editor = $this->createUser('editor@cuisine.fr', 'Editeur', 'editor', 2, ['ROLE_EDITOR'], $faker);
        $manager->persist($editor);
        $this->addReference('user_editor', $editor);

        for ($i = 1; $i <= 8; $i++) {
            $user = $this->createUser(
                $faker->unique()->safeEmail(),
                $faker->firstName(),
                $faker->lastName(),
                $faker->numberBetween(1000, 9999),
                [],
                $faker
            );
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }

    private function createUser(string $email, string $firstName, string $pseudo, int $pseudoId, array $roles, Generator $faker): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($faker->lastName());
        $user->setPseudo($pseudo);
        $user->setPseudoId($pseudoId);
        $user->setRoles($roles);
        $user->setPassword($this->hasher->hashPassword($user, 'password'));

        return $user;
    }
}
