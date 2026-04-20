<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'app_panier_index')]
    public function index(SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $panier = $session->get('panier', []);
        $dataPanier = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $produit = $produitRepository->find($id);

            if ($produit) {
                $dataPanier[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                    'sousTotal' => $produit->getPrix() * $quantite,
                ];

                $total += $produit->getPrix() * $quantite;
            }
        }

        return $this->render('panier/index.html.twig', [
            'dataPanier' => $dataPanier,
            'total' => $total,
        ]);
    }

    #[Route('/add/{id}', name: 'app_panier_add', requirements: ['id' => '\d+'])]
    public function add(int $id, SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        $panier = $session->get('panier', []);

        if (isset($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);

        $this->addFlash('success', 'Produit ajoute au panier.');

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/remove/{id}', name: 'app_panier_remove', requirements: ['id' => '\d+'])]
    public function remove(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (isset($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/increase/{id}', name: 'app_panier_increase', requirements: ['id' => '\d+'])]
    public function increase(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (isset($panier[$id])) {
            $panier[$id]++;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/decrease/{id}', name: 'app_panier_decrease', requirements: ['id' => '\d+'])]
    public function decrease(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (isset($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/clear', name: 'app_panier_clear')]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('panier');

        return $this->redirectToRoute('app_panier_index');
    }
}