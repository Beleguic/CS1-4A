<?php

namespace App\Controller\Back;

use App\Entity\Company;
use App\Form\Back\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/company')]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'app_company', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $companyRepository->paginationQuery(),
            $request->query->get('page', 1),
            20
        );

        return $this->render('back/company/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/new', name: 'app_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($company);
            $entityManager->flush();
            return $this->redirectToRoute('back_app_company', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/company/new.html.twig', [
            'company' => $company,
            'form' => $form,
            'is_edit' => false
        ]);
    }

    #[Route('/{id}', name: 'app_company_show', methods: ['GET'])]
    public function show(Company $company): Response
    {
        return $this->render('back/company/show.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('back_app_company', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/company/edit.html.twig', [
            'company' => $company,
            'form' => $form,
            'is_edit' => true
        ]);
    }

    #[Route('/delete/{id}', name: 'app_company_delete', methods: ['POST'])]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('back_app_company', [], Response::HTTP_SEE_OTHER);
    }
}
