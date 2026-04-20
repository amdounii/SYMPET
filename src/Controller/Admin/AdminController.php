<?php

namespace App\Controller\Admin;

use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        CommandeRepository $commandeRepository,
        UserRepository $userRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $produits = $produitRepository->findAll();
        $categories = $categorieRepository->findAll();
        $commandes = $commandeRepository->findAll();
        $users = $userRepository->findAll();

        $nombreProduits = count($produits);
        $nombreCategories = count($categories);
        $nombreCommandes = count($commandes);
        $nombreClients = count($users);

        $chiffreAffaires = 0;
        $commandesEnAttente = 0;
        $commandesEnCours = 0;
        $commandesCompletees = 0;

        foreach ($commandes as $commande) {
            $chiffreAffaires += $commande->getTotal() ?? 0;

            if ($commande->getStatut() === 'en_attente') {
                $commandesEnAttente++;
            } elseif ($commande->getStatut() === 'en_cours') {
                $commandesEnCours++;
            } elseif ($commande->getStatut() === 'completee') {
                $commandesCompletees++;
            }
        }

        return $this->render('admin/index.html.twig', [
            'nombreProduits' => $nombreProduits,
            'nombreCategories' => $nombreCategories,
            'nombreCommandes' => $nombreCommandes,
            'nombreClients' => $nombreClients,
            'chiffreAffaires' => $chiffreAffaires,
            'commandesEnAttente' => $commandesEnAttente,
            'commandesEnCours' => $commandesEnCours,
            'commandesCompletees' => $commandesCompletees,
        ]);
    }

    #[Route('/produits', name: 'app_admin_produits')]
    public function produits(ProduitRepository $produitRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/produits.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/categories', name: 'app_admin_categories')]
    public function categories(CategorieRepository $categorieRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/categories.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/commandes', name: 'app_admin_commandes')]
    public function commandes(CommandeRepository $commandeRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/commandes.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
}