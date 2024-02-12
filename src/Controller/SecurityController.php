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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
            // Générer et stocker un token unique pour l'activation du compte
            $token = bin2hex(random_bytes(32)); // Génère un token unique
            $user->setActivationToken($token);
    
            // Envoyer un e-mail de confirmation avec le lien d'activation
            $activationLink = $this->generateUrl('activate_account', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
    
            $senderName = 'Plumb Pay';
            $senderEmail = 'team_plombpay@outlook.com';
            $recipientName = $user->getUserIdentifier();
            $recipientEmail = $user->getEmail();
            $subject = 'Confirmation d\'inscription';
            $htmlContent = '<html><head></head><body><p>Bienvenue sur notre site !</p><p>Veuillez cliquer sur le lien suivant pour activer votre compte : <a href="' . $activationLink . '">Activer votre compte</a></p></body></html>';
    
            // Envoyer l'e-mail de confirmation
            $response = $emailService->sendEmail($senderName, $senderEmail, $recipientName, $recipientEmail, $subject, $htmlContent);
    
            if ($response['success']) {
                // L'e-mail de confirmation a été envoyé avec succès, on peut maintenant stocker l'utilisateur
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
    
                $entityManager->persist($user);
                $entityManager->flush();
    
                return $this->redirectToRoute('app_login');
            } else {
                // Une erreur s'est produite lors de l'envoi de l'e-mail
                return $this->redirectToRoute('app_register');
            }
        }
    
        return $this->render('security/registration/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    #[Route('/activate-account/{token}', name: 'activate_account')]
    public function activateAccount($token, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['activationToken' => $token]);

        if (!$user) {
            // Gérer le cas où le token n'est pas valide
            return $this->redirectToRoute('app_login');
        }

        // Activer le compte de l'utilisateur en supprimant le token d'activation
        $user->setActivationToken(null);
        $user->setEnabled(true);

        $entityManager->flush();

        // Redirigez l'utilisateur vers une page de confirmation ou affichez un message de succès
        return $this->redirectToRoute('app_login');
    }
}