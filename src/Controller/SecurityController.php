<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\BrevoEmailService;
class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login/index.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, BrevoEmailService $emailService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // Envoyer un e-mail de confirmation
            $senderName = 'Plumb Pay';
            $senderEmail = 'team_plombpay@outlook.com';
            $recipientName = $user->getUserIdentifier();
            $recipientEmail = $user->getEmail();
            $subject = 'Confirmation d\'inscription';
            $htmlContent = '<html><head></head><body><p>Bienvenue sur notre site !</p><p>Votre inscription a été confirmée.</p></body></html>';

            $response = $emailService->sendEmail($senderName, $senderEmail, $recipientName, $recipientEmail, $subject, $htmlContent);

            // Gérer la réponse de l'envoi de l'e-mail
            if ($response['success']) {
                // L'e-mail de confirmation a été envoyé avec succès
                return $this->redirectToRoute('security/login/index.html.twig');
            } else {
                // Une erreur s'est produite lors de l'envoi de l'e-mail
                return $this->redirectToRoute('error_page');
            }
        }

        return $this->render('security/registration/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}