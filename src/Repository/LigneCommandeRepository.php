<?php

namespace App\Repository;

use App\Entity\LigneCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LigneCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }

    public function findByCommande(int $commandeId): array
    {
        return $this->createQueryBuilder('lc')
            ->join('lc.commande', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $commandeId)
            ->getQuery()
            ->getResult();
    }

    public function getProduitsPlusVendus(int $limit = 5): array
    {
        return $this->createQueryBuilder('lc')
            ->select('p.nom AS produit, SUM(lc.quantite) AS totalVendu')
            ->join('lc.produit', 'p')
            ->groupBy('p.id')
            ->orderBy('totalVendu', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}