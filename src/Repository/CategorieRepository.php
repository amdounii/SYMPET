<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    // public function findByNom(string $nom): array
    // {
    //     return $this->createQueryBuilder('c')
    //         ->andWhere('c.nom LIKE :nom')
    //         ->setParameter('nom', '%' . $nom . '%')
    //         ->orderBy('c.nom', 'ASC')
    //         ->getQuery()
    //         ->getResult();
    // }
}