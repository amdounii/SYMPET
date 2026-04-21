<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    #[Route('/reset-password', name: 'app_forgot_password_request')]
    public function index(): Response
    {
        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/reset-password/process', name: 'app_reset_password_process', methods: ['POST'])]
    public function process(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $email = $request->request->get('email');
        $newPassword = $request->request->get('password');

        $user = $userRepository->findOneByEmail($email);

        if ($user) {
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_login');
    }
}