<?php

namespace App\Controller\Admin;

use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(
        CommandeRepository $commandeRepository,
        UserRepository $userRepository,
        ProduitRepository $produitRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/admin_dashboard/index.html.twig', [
            'chiffreAffaire' => $commandeRepository->getChiffreAffaire(),
            'nbClients' => $userRepository->countUsers(),
            'topProduits' => $produitRepository->findTopProduits(),
        ]);
    }
}