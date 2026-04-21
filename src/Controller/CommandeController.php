<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/valider', name: 'app_commande_valider')]
    public function valider(
        RequestStack $requestStack,
        ProduitRepository $produitRepository,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $session = $requestStack->getSession();
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            return $this->redirectToRoute('app_panier');
        }

        $commande = new Commande();
        $commande->setNumero('CMD-' . time());
        $commande->setDateCommande(new \DateTimeImmutable());
        $commande->setStatut('en_attente');
        $commande->setModePaiement('à la livraison');
        $commande->setUser($this->getUser());

        $total = 0;

        foreach ($panier as $id => $quantite) {
            $produit = $produitRepository->find($id);

            if ($produit && $produit->getStock() >= $quantite) {
                $ligne = new LigneCommande();
                $ligne->setCommande($commande);
                $ligne->setProduit($produit);
                $ligne->setQuantite($quantite);
                $ligne->setPrixUnitaire($produit->getPrix());

                $produit->setStock($produit->getStock() - $quantite);

                $total += $produit->getPrix() * $quantite;

                $entityManager->persist($ligne);
            }
        }

        $commande->setTotal($total);

        $entityManager->persist($commande);
        $entityManager->flush();

        $session->remove('panier');

        return $this->redirectToRoute('app_commande_historique');
    }

    #[Route('/historique', name: 'app_commande_historique')]
    public function historique(CommandeRepository $commandeRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $commandes = $commandeRepository->findByUser($this->getUser()->getId());

        return $this->render('commande/historique.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/show/{id}', name: 'app_commande_show')]
    public function show(int $id, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable');
        }

        if ($commande->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }
}