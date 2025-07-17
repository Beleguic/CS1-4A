<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\RequestNewCompanyUser;
use App\Entity\User;
use App\Form\Front\RegisterUserFromCompanyType;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\BrevoEmailService;
use App\Service\SecurityLoggerService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, SecurityLoggerService $securityLogger): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        // Logger la tentative de connexion
        if ($error) {
            $securityLogger->logLoginAttempt($lastUsername, false);
        }
        
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
        $user->setVerifiedAt(new \DateTimeImmutable());
        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }

    #[Route('/register-account/{id}', name: 'register_account')]
    public function registerAccount($id, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, BrevoEmailService $emailService): Response
    {
        $newCompanyUser = $entityManager->getRepository(RequestNewCompanyUser::class)->findOneBy(['id'=>$id]);
        if (!$newCompanyUser) {
            throw new NotFoundHttpException('This page does not exist.');
        }

        $user = new User();
        $form = $this->createForm(RegisterUserFromCompanyType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $companyId = $newCompanyUser->getCompanyId();
            $company = $entityManager->getRepository(Company::class)->findOneBy(['id'=>$companyId]);
            $user->setCompanyId($company->getId());
            $user->setEmail($newCompanyUser->getEmail());
            $user->setRoles([$newCompanyUser->getRole()]);

            $token = bin2hex(random_bytes(32));
            $user->setActivationToken($token);

            $activationLink = $this->generateUrl('activate_account', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            $senderName = 'Plumbpay';
            $senderEmail = 'team_plumbpay@outlook.com';
            $recipientName = $user->getUserIdentifier();
            $recipientEmail = $user->getEmail();
            $subject = 'Confirmation d\'inscription';
            $htmlContent = '<html><head></head><body><p>Bienvenue sur notre site !</p><p>Veuillez cliquer sur le lien suivant pour activer votre compte : <a href="' . $activationLink . '">Activer votre compte</a></p></body></html>';
            $response = $emailService->sendEmail($senderName, $senderEmail, $recipientName, $recipientEmail, $subject, $htmlContent);
            if ($response['success']) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $user->setCompanyId($companyId);
                $entityManager->flush();
                return $this->redirectToRoute('app_login');
            } else {
                return $this->redirectToRoute('register_account', ['id'=>$id]);
            }
        }
        return $this->render('security/registrationFromCompany/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/activate-user-company/{id}', name: 'activate_user_company')]
    public function activateUserCompany($id, EntityManagerInterface $entityManager): Response
    {
        $newCompanyUser = $entityManager->getRepository(RequestNewCompanyUser::class)->findOneBy(['id'=>$id]);
        if (!$newCompanyUser) {
            throw new NotFoundHttpException('This page does not exist.');
        }

        $userEmail = $newCompanyUser->getEmail();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email'=>$userEmail]);

        if (!$user) {
            throw new NotFoundHttpException('This page does not exist.');
        }

        $companyId = $newCompanyUser->getCompanyId();
        $company = $entityManager->getRepository(Company::class)->findOneBy(['id'=>$companyId]);
        $companyName = $company->getName();
        $user->setCompanyId($newCompanyUser->getCompanyId());
        $user->setRoles([$newCompanyUser->getRole()]);
        $entityManager->flush();
        $this->addFlash('success', "You have been added to ". $companyName . " !");
        return $this->redirectToRoute('app_login');
    }

    #[Route('/forgot-password', name: 'forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $entityManager, BrevoEmailService $emailService): Response
    {
        $successMessage = null;

    if ($request->isMethod('POST')) {
        $email = $request->request->get('email');
        
        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Adresse email invalide.');
            return $this->render('security/forgot_password.html.twig', [
                'successMessage' => null,
            ]);
        }

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
                // Message de succès
                $successMessage = 'Un email de réinitialisation a été envoyé à votre adresse.';
            } else {
                // Gérer les erreurs d'envoi d'e-mail
                return $this->redirectToRoute('app_register');
            }
        }
    }

    return $this->render('security/forgot_password.html.twig', [
        'successMessage' => $successMessage,
    ]);

    }

    #[Route('/reset-password/{token}', name: 'reset_password_new')]
    public function resetPasswordNew(Request $request, $token, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetPasswordToken' => $token]);
        if (!$user instanceof UserInterface) {
            // Gérer le cas où le token est invalide
            return $this->redirectToRoute('app_login', ['error' => 'Token invalide ou expiré']);
        }
        
        // Vérifier l'expiration du token
        if ($user->isResetPasswordTokenExpired()) {
            $user->setResetPasswordToken(null);
            $user->setResetPasswordTokenExpiresAt(null);
            $entityManager->flush();
            return $this->redirectToRoute('app_login', ['error' => 'Token expiré']);
        }
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            $user->setResetPasswordToken(null);
            $entityManager->flush();
            return $this->redirectToRoute('app_login', ['success' => 'Password reset successfully']);
        }

        return $this->render('security/reset_password/new.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }
}
