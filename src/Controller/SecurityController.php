<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\RequestNewCompanyUser;
use App\Entity\User;
use App\Entity\Role;
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
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        BrevoEmailService $emailService
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Créer une nouvelle entreprise
            $company = new Company();
            $company->setName($form->get('companyName')->getData());
            $company->setEmail($form->get('companyEmail')->getData());
            $company->setAddressName($form->get('companyAddress')->getData());
            $company->setAddressCity($form->get('companyCity')->getData());
            $company->setAddressZipCode($form->get('companyZipCode')->getData());
            $company->setAddressCountry('France');
            $company->setInvoiceEmail($form->get('companyEmail')->getData());
            
            // Persister l'entreprise d'abord
            $entityManager->persist($company);
            $entityManager->flush();

            // Récupérer le rôle admin
            $roleRepository = $entityManager->getRepository(Role::class);
            $adminRole = $roleRepository->findOneBy(['value' => 'ROLE_ADMIN']);
            
            // Compte désactivé par défaut
            $user->setEnabled(false);
            $user->setCompanyId($company->getId());
            $user->setCompany($company);
            
            // Assigner le rôle admin
            if ($adminRole) {
                $user->setRoles(['ROLE_ADMIN']);
            } else {
                $user->setRoles(['ROLE_USER']); // Fallback si le rôle admin n'existe pas
            }

            // Générer un token d'activation
            $token = bin2hex(random_bytes(32));
            $user->setActivationToken($token);

            // Hasher le mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // Envoyer l'email d'activation
            $activationLink = $this->generateUrl('activate_account', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            $response = $emailService->sendEmail(
                'Plumbpay',
                'team_plumbpay@outlook.com',
                $user->getUserIdentifier(),
                $user->getEmail(),
                'Confirmation d\'inscription',
                "<p>Bienvenue ! Cliquez ici pour activer votre compte : <a href=\"$activationLink\">Activer</a></p>"
            );

            if (!$response['success']) {
                $this->addFlash('error', 'Erreur envoi email : ' . $response['error']);
            } else {
                $this->addFlash('success', 'Un email d’activation vous a été envoyé.');
            }
            return $this->redirectToRoute('app_login');
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
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_login');
        }

        $user->setActivationToken(null);
        $user->setEnabled(true); // active le compte
        $user->setVerifiedAt(new \DateTimeImmutable()); // optionnel : date de vérification
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte est maintenant activé !');
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
            
            // Configurer l'utilisateur AVANT l'envoi d'email
            $user->setCompanyId($company->getId());
            $user->setCompany($company); // Assigner aussi la relation
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
                // Hasher le mot de passe et sauvegarder
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                
                // Supprimer la demande d'invitation après création réussie
                $entityManager->remove($newCompanyUser);
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

    #[Route('/activate-employee-company/{id}', name: 'activate_employee_company')]
    public function activateEmployeeCompany($id, EntityManagerInterface $entityManager): Response
    {
        $newCompanyUser = $entityManager->getRepository(RequestNewCompanyUser::class)->findOneBy(['id' => $id]);
        
        if (!$newCompanyUser) {
            $this->addFlash('error', 'Invitation invalide ou expirée.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur existe déjà
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $newCompanyUser->getEmail()]);
        
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur n'est pas déjà dans cette entreprise
        if ($user->getCompanyId() && $user->getCompanyId() == $newCompanyUser->getCompanyId()) {
            $this->addFlash('info', 'Vous êtes déjà membre de cette entreprise.');
            return $this->redirectToRoute('app_login');
        }

        // Ajouter l'utilisateur à l'entreprise
        $user->setCompanyId($newCompanyUser->getCompanyId());
        
        // Récupérer l'entreprise pour la liaison
        $company = $entityManager->getRepository(Company::class)->find($newCompanyUser->getCompanyId());
        if ($company) {
            $user->setCompany($company);
        }

        // Ajouter le nouveau rôle (en plus des rôles existants)
        $currentRoles = $user->getRoles();
        $newRole = $newCompanyUser->getRole();
        if (!in_array($newRole, $currentRoles)) {
            $currentRoles[] = $newRole;
            $user->setRoles($currentRoles);
        }

        // Supprimer la demande d'invitation
        $entityManager->remove($newCompanyUser);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez été ajouté avec succès à l\'entreprise !');
        return $this->redirectToRoute('app_login');
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
            return $this->redirectToRoute('app_login', ['token' => $token, 'error' => 'Invalid or expired token']);
        
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
