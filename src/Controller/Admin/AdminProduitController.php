<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/produit')]
class AdminProduitController extends AbstractController
{
    #[Route('/', name: 'admin_produit_index')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/admin_produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_produit_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        CategorieRepository $categorieRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $produit = new Produit();
            $produit->setNom($request->request->get('nom'));
            $produit->setDescription($request->request->get('description'));
            $produit->setPrix((float) $request->request->get('prix'));
            $produit->setStock((int) $request->request->get('stock'));
            $produit->setImage($request->request->get('image'));

            $categorie = $categorieRepository->find($request->request->get('categorie'));
            $produit->setCategorie($categorie);

            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('admin_produit_index');
        }

        return $this->render('admin/admin_produit/new.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_produit_edit')]
    public function edit(
        int $id,
        Request $request,
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable');
        }

        if ($request->isMethod('POST')) {
            $produit->setNom($request->request->get('nom'));
            $produit->setDescription($request->request->get('description'));
            $produit->setPrix((float) $request->request->get('prix'));
            $produit->setStock((int) $request->request->get('stock'));
            $produit->setImage($request->request->get('image'));

            $categorie = $categorieRepository->find($request->request->get('categorie'));
            $produit->setCategorie($categorie);

            $entityManager->flush();

            return $this->redirectToRoute('admin_produit_index');
        }

        return $this->render('admin/admin_produit/edit.html.twig', [
            'produit' => $produit,
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_produit_delete')]
    public function delete(
        int $id,
        ProduitRepository $produitRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $produit = $produitRepository->find($id);

        if ($produit) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_produit_index');
    }
}