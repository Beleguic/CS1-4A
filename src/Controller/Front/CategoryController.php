<?php

namespace App\Controller\Front;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $user = $this->getUser();
        $companyId = $user->getCompanyId();

        return $this->render('front/category/index.html.twig', [
            'categories' => $categoryRepository->findByCompagny($companyId),
            
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $companyId = $user->getCompanyId();

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category, [
            'company_id' => $companyId
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCompanyId($companyId);
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('front_app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('front/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $companyId = $user->getCompanyId();
        
        $form = $this->createForm(CategoryType::class, $category, [
            'company_id' => $companyId
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('front_app_category_show', ['id' => $category->getId()], Response::HTTP_FOUND);
        }

        return $this->render('front/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            try {
                $categoryName = $category->getName();
                $entityManager->remove($category);
                $entityManager->flush();
                $this->addFlash('success', 'La catégorie "' . $categoryName . '" a été supprimée avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Token de sécurité invalide.');
        }

        return $this->redirectToRoute('front_app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
