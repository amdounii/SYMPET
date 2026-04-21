<?php

namespace App\Controller\Admin;

use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/commande')]
class AdminCommandeController extends AbstractController
{
    #[Route('/', name: 'admin_commande_index')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/admin_commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/show/{id}', name: 'admin_commande_show')]
    public function show(int $id, CommandeRepository $commandeRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $commande = $commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable');
        }

        return $this->render('admin/admin_commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/statut/{id}', name: 'admin_commande_statut', methods: ['POST'])]
    public function statut(
        int $id,
        Request $request,
        CommandeRepository $commandeRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $commande = $commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable');
        }

        $commande->setStatut($request->request->get('statut'));
        $entityManager->flush();

        return $this->redirectToRoute('admin_commande_show', [
            'id' => $commande->getId(),
        ]);
    }
}