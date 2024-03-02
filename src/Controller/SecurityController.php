<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\BrevoEmailService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
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
    
            $senderName = 'Plumbpay';
            $senderEmail = 'team_plumbpay@outlook.com';
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

            return $this->redirectToRoute('app_login');
        }

        $user->setActivationToken(null);
        $user->setEnabled(true);

        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }


    #[Route('/forgot-password', name: 'forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $entityManager, BrevoEmailService $emailService): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user instanceof UserInterface) {

                $token = bin2hex(random_bytes(32)); // Génère un token unique
                $user->setResetPasswordToken($token);
                $entityManager->flush();

                // Envoyer un e-mail de réinitialisation avec le lien de réinitialisation
                $resetLink = $this->generateUrl('reset_password_new', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $senderName = 'Plumbpay';
                $senderEmail = 'team_plumbpay@outlook.com';
                $recipientName = $user->getUserIdentifier();
                $recipientEmail = $user->getEmail();
                $subject = 'Réinitialisation du mot de passe';
                $htmlContent = '<html><head></head><body><p>Veuillez cliquer sur le lien suivant pour réinitialiser votre mot de passe : <a href="' . $resetLink . '">Réinitialiser votre mot de passe</a></p></body></html>';

                // Envoyer l'e-mail de réinitialisation
                $response = $emailService->sendEmail($senderName, $senderEmail, $recipientName, $recipientEmail, $subject, $htmlContent);

                if ($response['success']) {
                    // Rediriger vers une page de confirmation
                    return $this->redirectToRoute('reset_password_new', ['token' => $token]);
                    
                } else {
                    // Gérer les erreurs d'envoi d'e-mail
                    return $this->redirectToRoute('app_register');
                }
            }
        }

        return $this->render('security/forgot_password.html.twig');
    }




    #[Route('/reset-password/{token}', name: 'reset_password_new')]
    public function resetPasswordNew(Request $request, $token, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetPasswordToken' => $token]);

        if (!$user instanceof UserInterface) {
            // Gérer le cas où le token est invalide
            return $this->redirectToRoute('app_login', ['token' => $token, 'error' => 'Invalid or expired token']);
        
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le mot de passe de l'utilisateur
            $newPassword = $form->get('newPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            #$user->setResetPasswordToken(null); // Supprimer le token de réinitialisation
            $entityManager->flush();

            // Rediriger vers une page de confirmation ou de connexion
            return $this->redirectToRoute('app_login', ['success' => 'Password reset successfully']);
        }

        return $this->render('security/reset_password/new.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }
    }