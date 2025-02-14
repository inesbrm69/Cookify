<?php

namespace App\Repository;

use App\Entity\Recipes;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecipesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipes::class);
    }

    public function findByDiet(string $diet): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.categories', 'c')
            ->where('c.name = :diet')
            ->setParameter('diet', $diet)
            ->getQuery()
            ->getResult();
    }

    public function findByAllergy(string $allergy): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.categories', 'c')
            ->where('c.name = :allergy')
            ->setParameter('allergy', $allergy)
            ->getQuery()
            ->getResult();
    }


}