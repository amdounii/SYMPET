<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findByNom(string $nom): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.nom LIKE :nom')
            ->setParameter('nom', '%' . $nom . '%')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCategorie(int $categorieId): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.categorie', 'c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $categorieId)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findProduitsDisponibles(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.stock > 0')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}