<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/categorie')]
class AdminCategorieController extends AbstractController
{
    #[Route('/', name: 'admin_categorie_index')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/admin_categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_categorie_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $categorie = new Categorie();
            $categorie->setNom($request->request->get('nom'));
            $categorie->setDescription($request->request->get('description'));

            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('admin_categorie_index');
        }

        return $this->render('admin/admin_categorie/new.html.twig');
    }

    #[Route('/edit/{id}', name: 'admin_categorie_edit')]
    public function edit(
        int $id,
        Request $request,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie introuvable');
        }

        if ($request->isMethod('POST')) {
            $categorie->setNom($request->request->get('nom'));
            $categorie->setDescription($request->request->get('description'));

            $entityManager->flush();

            return $this->redirectToRoute('admin_categorie_index');
        }

        return $this->render('admin/admin_categorie/edit.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_categorie_delete')]
    public function delete(
        int $id,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categorie = $categorieRepository->find($id);

        if ($categorie) {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_categorie_index');
    }
}