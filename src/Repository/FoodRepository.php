<?php

namespace App\Repository;

use App\Entity\Food;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Food>
 *
 * @method Food|null find($id, $lockMode = null, $lockVersion = null)
 * @method Food|null findOneBy(array $criteria, array $orderBy = null)
 * @method Food[]    findAll()
 * @method Food[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Food::class);
    }

    /**
     * Find food items by name (case insensitive).
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('f')
            ->where('LOWER(f.name) LIKE LOWER(:name)')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the most expensive food items.
     */
    public function findMostExpensive(int $limit = 5): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.price', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the cheapest food items.
     */
    public function findCheapest(int $limit = 5): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.price', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
