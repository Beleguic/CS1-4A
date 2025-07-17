<?php

namespace App\Controller\Front;

use App\Entity\Company;
use App\Entity\RequestNewCompanyUser;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\Front\CompanyType;
use App\Form\Front\RequestNewCompanyUserType;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use App\Service\BrevoEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/company')]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'app_company', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('This page does not exist');
        }

        $company = $user->getCompany();
        if (!$company) {
            throw $this->createNotFoundException('This page does not exist');
        }
        return $this->render('front/company/index.html.twig', [
            'company' => $company,
            'user' => $user
        ]);
    }

    #[Route('/edit', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, CompanyRepository $companyRepository): Response
    {

        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('Page non trouvée');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès refusé');
        }

        $companyId = $user->getCompanyId();
        $company = $companyRepository->find($companyId);

        if (!$company) {
            throw new NotFoundHttpException('This page does not exist.');
        }

        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('front_app_company', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/company/edit.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    #[Security('is_granted("ROLE_ADMIN")')]
    #[Route('/add/employee', name: 'app_company_add_employee', methods: ['GET', 'POST'])]
    public function add_user(Request $request, EntityManagerInterface $entityManager, CompanyRepository $companyRepository, BrevoEmailService $emailService, SessionInterface $session,  ManagerRegistry $registry): Response
    {
        $newCompanyUser = new RequestNewCompanyUser();
        $form = $this->createForm(RequestNewCompanyUserType::class, $newCompanyUser);
        $form->handleRequest($request);
        $companyId = $this->getUser()->getCompanyId();
        $company = $registry->getRepository(Company::class)->find($companyId);

        if ($form->isSubmitted() && $form->isValid()) {
            $userId = $this->getUser()->getId();
            $companyId = $this->getUser()->getCompanyId();
            $newCompanyUser->setUserId($userId);
            $newCompanyUser->setCompanyId($companyId);
            $entityManager->persist($newCompanyUser);
            $entityManager->flush();

            $company = $registry->getRepository(Company::class)->find($companyId);
            $companyName = $company->getName();
            $id = $newCompanyUser->getId();

            $employeeEmail = $newCompanyUser->getEmail();
            $checkUser = $registry->getRepository(User::class)->findOneBy(["email" => $employeeEmail]);
            if ($checkUser == null)
                $activationLink = $this->generateUrl('register_account', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL);
            else {
                $activationLink = $this->generateUrl('activate_employee_company', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL);
            }
            $senderName = 'Plumbpay';
            $senderEmail = 'team_plumbpay@outlook.com';
            $recipientName = $this->getUser()->getUserIdentifier();
            $recipientEmail = $newCompanyUser->getEmail();
            $subject = "Plumbpay - " . $companyName . " vous a ajouté !";
            $htmlContent = "<html><head></head><body><p> La société " . $companyName . " vous a ajouté!</p><p>Veuillez cliquer sur le lien suivant pour être ajouté à " . $companyName . " : <a href='" . $activationLink . "'>Etre ajouté</a></p></body></html>";

            // Envoyer l'e-mail de confirmation
            $response = $emailService->sendEmail($senderName, $senderEmail, $recipientName, $recipientEmail, $subject, $htmlContent);

            if ($response['success']) {
                $this->addFlash('success', "An email has been sent to " . $newCompanyUser->getEmail() . " !");
            } else {
                $this->addFlash('success', "An error has been occurred. Please retry !");
            }
        }

        return $this->render('front/company/add_employee.html.twig', [
            'form' => $form,
            'company' => $company
        ]);
    }

    #[Route('/list/employee', name: 'app_company_list_employee', methods: ['GET'])]
    public function listEmployee(UserRepository $userRepository, ManagerRegistry $registry): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('Page non trouvée');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès refusé');
        }

        $companyId = $user->getCompanyId();
        $company = $registry->getRepository(Company::class)->find($companyId);

        return $this->render('front/company/list.html.twig', [
            'users' => $userRepository->findByCompagny($companyId),
            'company' => $company
        ]);
    }

    #[Route('/edit/employee', name:'app_company_edit_employee', methods: ['GET', 'POST'])]
    public function editEmployee(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if(($oldPassword != null && $newPassword != null) || ($oldPassword != "" && $newPassword != "")){
                if ($passwordHasher->isPasswordValid($user, $form->get('oldPassword')->getData())) {
                    $newEncodedPassword = $passwordHasher->hashPassword($user, $form->get('newPassword')->getData());
                    $user->setPassword($newEncodedPassword);
                    $manager->flush();
                    $this->addFlash('success', 'Mot de passe mis à jour avec succès');
                } else {
                    $this->addFlash('error', 'Ancien mot de passe incorrect.');
                }
            }

            $manager->flush();
            $this->addFlash('notice', 'Votre compte a été mis-à-jour !');

        }

        return $this->render('/front/company/edit_employee.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/employee', name: 'app_company_delete_employee', methods: ['GET'])]
    public function deleteEmployee(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_app_company_list_employee', [], Response::HTTP_SEE_OTHER);
    }

}