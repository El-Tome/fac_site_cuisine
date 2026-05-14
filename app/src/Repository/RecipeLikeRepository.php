<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\RecipeLike;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RecipeLike>
 */
class RecipeLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeLike::class);
    }

    public function findOneByUserAndRecipe(User $user, Recipe $recipe): ?RecipeLike
    {
        return $this->findOneBy(['user' => $user, 'recipe' => $recipe]);
    }

    public function countByRecipe(Recipe $recipe): int
    {
        return $this->count(['recipe' => $recipe]);
    }

    /** @return RecipeLike[] */
    public function findByUserOrderedByRecent(User $user): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.user = :user')
            ->setParameter('user', $user)
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
