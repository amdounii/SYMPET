<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer
    ): Response {
        if ($request->isMethod('POST')) {
            $user = new User();
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
            $user->setEmail($request->request->get('email'));
            $user->setTelephone($request->request->get('telephone'));
            $user->setAdresse($request->request->get('adresse'));
            $user->setRoles(['ROLE_USER']);
            $user->setIsVerified(false);

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $request->request->get('password')
            );

            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();
$email = (new Email())
    ->from('test@sympet.com')
    ->to($user->getEmail())
    ->subject('Confirmation de votre inscription')
    ->html("
        <h2>Bienvenue sur SymPet</h2>
        <p>Merci pour votre inscription.</p>
        <p>
            <a href='http://127.0.0.1:8000/verify-email/{$user->getId()}'>
                Confirmer mon compte
            </a>
        </p>
    ");

try {
    $mailer->send($email);
} catch (\Exception $e) {
    dd($e->getMessage());
}

return $this->redirectToRoute('app_login');

// PAS DE dd('EMAIL ENVOYE') ici

return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig');
    }

    #[Route('/verify-email/{id}', name: 'app_verify_email')]
    public function verifyEmail(
        int $id,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable');
        }

        $user->setIsVerified(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }
}