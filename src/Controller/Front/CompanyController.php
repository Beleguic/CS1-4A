<?php

namespace App\Controller\Front;

use App\Entity\Company;
use App\Form\Front\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
}
