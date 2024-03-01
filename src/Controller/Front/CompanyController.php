<?php

namespace App\Controller\Front;

use App\Entity\Company;
use App\Entity\RequestNewCompanyUser;
use App\Entity\User;
use App\Form\Front\CompanyType;
use App\Form\Front\RequestNewCompanyUserType;
use App\Repository\CompanyRepository;
use App\Service\BrevoEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
            throw $this->createNotFoundException('This page does not exist');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException('This page does not exist');
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
            $checkUser = $registry->getRepository(User::class)->findOneBy(["email"=>$employeeEmail]);
            if($checkUser == null)
                $activationLink = $this->generateUrl('register_account', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL);
            else {
                $activationLink = $this->generateUrl('activate_employee_company', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL);
            }
            $senderName = 'Plumbpay';
            $senderEmail = 'team_plumbpay@outlook.com';
            $recipientName = $this->getUser()->getUserIdentifier();
            $recipientEmail = $newCompanyUser->getEmail();
            $subject = "Plumbpay - ".$companyName." vous a ajouté !";
            $htmlContent = "<html><head></head><body><p> La société ". $companyName ." vous a ajouté!</p><p>Veuillez cliquer sur le lien suivant pour être ajouté à ".$companyName." : <a href='". $activationLink . "'>Etre ajouté</a></p></body></html>";

            // Envoyer l'e-mail de confirmation
            $response = $emailService->sendEmail($senderName, $senderEmail, $recipientName, $recipientEmail, $subject, $htmlContent);

            if ($response['success']) {
                $this->addFlash('success', "An email has been sent to ". $newCompanyUser->getEmail() . " !");
            } else {
                $this->addFlash('success', "An error has been occurred. Please retry !");
            }
        }

        return $this->render('front/company/add_employee.html.twig', [
            'form' => $form,
            'company' => $company
        ]);
    }
}