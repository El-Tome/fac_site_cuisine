<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findFeatured(): ?Recipe
    {
        return $this->findOneBy(['featured' => true]);
    }

    public function clearFeatured(): void
    {
        $this->createQueryBuilder('r')
            ->update()
            ->set('r.featured', ':false')
            ->setParameter('false', false)
            ->getQuery()
            ->execute();
    }

    /** @return Recipe[] */
    public function findRandom(int $limit): array
    {
        $ids = $this->createQueryBuilder('r')
            ->select('r.id')
            ->getQuery()
            ->getSingleColumnResult();

        if (empty($ids)) {
            return [];
        }

        shuffle($ids);
        $selectedIds = array_slice($ids, 0, $limit);

        return $this->createQueryBuilder('r')
            ->where('r.id IN (:ids)')
            ->setParameter('ids', $selectedIds)
            ->getQuery()
            ->getResult();
    }

    /** @return Recipe[] */
    public function findRandomExcluding(array $excludeIds, int $limit): array
    {
        $qb = $this->createQueryBuilder('r')->select('r.id');

        if (!empty($excludeIds)) {
            $qb->where('r.id NOT IN (:exclude)')->setParameter('exclude', $excludeIds);
        }

        $ids = $qb->getQuery()->getSingleColumnResult();

        if (empty($ids)) {
            return [];
        }

        shuffle($ids);
        $selectedIds = array_slice($ids, 0, $limit);

        return $this->createQueryBuilder('r')
            ->where('r.id IN (:ids)')
            ->setParameter('ids', $selectedIds)
            ->getQuery()
            ->getResult();
    }

    /** @return Recipe[] */
    public function search(string $query): array
    {
        $q = '%' . $query . '%';

        return $this->createQueryBuilder('r')
            ->join('r.author', 'a')
            ->where('r.title LIKE :q OR a.pseudo LIKE :q')
            ->setParameter('q', $q)
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Recipe[] Returns an array of Recipe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Recipe
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
