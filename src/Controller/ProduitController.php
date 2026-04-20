<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/produits', name: 'app_produit_index')]
    public function index(
        Request $request,
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository
    ): Response {
        $search = $request->query->get('search');
        $categorieId = $request->query->get('categorie');

        if ($search) {
            $produits = $produitRepository->findByNom($search);
        } elseif ($categorieId) {
            $produits = $produitRepository->findByCategorie((int) $categorieId);
        } else {
            $produits = $produitRepository->findAll();
        }

        $categories = $categorieRepository->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
            'search' => $search,
            'categorieId' => $categorieId,
        ]);
    }

    #[Route('/produit/{id}', name: 'app_produit_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }
}