<?php

namespace App\Controller\Front;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;


#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {

        $user = $this->getUser();
        $companyId = $user->getCompanyId();

        return $this->render('front/product/index.html.twig', [
            'products' => $productRepository->findByCompagny($companyId),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $companyId = $user->getCompanyId();

        $product = new Product();
        $product->setCompanyId($companyId);
        //dd($product);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCompanyId($companyId);
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('front_app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('front/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('front_app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    // src/Controller/Front/ProductController.php

    #[Route('/{id}/download-pdf', name: 'app_product_download_pdf', methods: ['GET'])]
    public function downloadPdf(Product $product): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($pdfOptions);
        
        $html = $this->renderView('front/product/pdf_template.html.twig', ['product' => $product]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $productNameSafe = preg_replace('/[^A-Za-z0-9\-]/', '_', $product->getName());

        $pdfFileName = "product_" . $productNameSafe . "_" . $product->getId() . ".pdf";

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$pdfFileName.'"',
        ]);
    }

}