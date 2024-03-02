<?php

namespace App\Controller\Back;

use App\Entity\Category;
use App\Entity\Company;
use App\Entity\Contact;
use App\Entity\Devis;
use App\Entity\Facture;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class IndexController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $cards = $this->generatedCards();

        return $this->render('back/index/index.html.twig', [
            'controller_name' => 'IndexController',
            "cards" => $cards
        ]);
    }

    private function generatedCards() : array
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $nbUser = $userRepository->count([]);
        $companyRepository = $this->entityManager->getRepository(Company::class);
        $nbCompany = $companyRepository->count([]);
        $contactRepository = $this->entityManager->getRepository(Contact::class);
        $nbContact = $contactRepository->count([]);
        $quotationRepository = $this->entityManager->getRepository(Devis::class);
        $nbQuotation = $quotationRepository->count([]);
        $invoiceRepository = $this->entityManager->getRepository(Facture::class);
        $nbInvoice = $invoiceRepository->count([]);
        $productRepository = $this->entityManager->getRepository(Product::class);
        $nbProduct = $productRepository->count([]);
        $categoryRepository = $this->entityManager->getRepository(Category::class);
        $nbCategory = $categoryRepository->count([]);

        $cards = [
            "general" => [
                1 => [
                    "nb" => $nbUser,
                    "link" => $this->generateUrl("back_app_user"),
                    "title" => "Users",
                    "icon" => "<svg xmlns='http://www.w3.org/2000/svg' class='w-full h-full' viewBox='0 0 24 24'><path fill='currentColor' d='M12 4a4 4 0 0 1 4 4a4 4 0 0 1-4 4a4 4 0 0 1-4-4a4 4 0 0 1 4-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4'/></svg>"
                ],
                2 => [
                    "nb" => $nbCompany,
                    "link" => $this->generateUrl("back_app_company"),
                    "title" => "Companies",
                    "icon" => "<svg xmlns='http://www.w3.org/2000/svg' class='w-full h-full' viewBox='0 0 24 24'><path fill='currentColor' d='M18 15h-2v2h2m0-6h-2v2h2m2 6h-8v-2h2v-2h-2v-2h2v-2h-2V9h8M10 7H8V5h2m0 6H8V9h2m0 6H8v-2h2m0 6H8v-2h2M6 7H4V5h2m0 6H4V9h2m0 6H4v-2h2m0 6H4v-2h2m6-10V3H2v18h20V7z'/></svg>"
                ],
                3 => [
                    "nb" => $nbContact,
                    "link" => $this->generateUrl("back_app_contact"),
                    "title" => "Contacts",
                    "icon" => "<svg xmlns='http://www.w3.org/2000/svg' class='w-full h-full' viewBox='0 0 24 24'><path fill='currentColor' d='M6 17c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1H6m9-9a3 3 0 0 1-3 3a3 3 0 0 1-3-3a3 3 0 0 1 3-3a3 3 0 0 1 3 3M3 5v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2'/></svg>"
                ]
            ],
            "pdf" => [
                1 => [
                    "nb" => $nbQuotation,
                    "link" => $this->generateUrl("back_app_user"),
                    "title" => "Quotations",
                    "icon" => "<svg xmlns='http://www.w3.org/2000/svg' class='w-full h-full' viewBox='0 0 24 24'><path fill='currentColor' d='M13 9V3.5L18.5 9M6 2c-1.11 0-2 .89-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z'/></svg>"
                ],
                2 => [
                    "nb" => $nbInvoice,
                    "link" => $this->generateUrl("back_app_company"),
                    "title" => "Invoices",
                    "icon" => "<svg xmlns='http://www.w3.org/2000/svg' class='w-full h-full' viewBox='0 0 24 24'><path fill='currentColor' d='M13 9V3.5L18.5 9M6 2c-1.11 0-2 .89-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z'/></svg>"
                ],
                3 => [
                    "nb" => $nbQuotation + $nbInvoice,
                    "link" => $this->generateUrl("back_app_company"),
                    "title" => "PDF Generated",
                    "icon" => "<svg xmlns='http://www.w3.org/2000/svg' class='w-full h-full' viewBox='0 0 512 512'><path fill='currentColor' d='M0 64C0 28.7 28.7 0 64 0h160v128c0 17.7 14.3 32 32 32h128v144H176c-35.3 0-64 28.7-64 64v144H64c-35.3 0-64-28.7-64-64zm384 64H256V0zM176 352h32c30.9 0 56 25.1 56 56s-25.1 56-56 56h-16v32c0 8.8-7.2 16-16 16s-16-7.2-16-16V368c0-8.8 7.2-16 16-16m32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24h-16v48zm96-80h32c26.5 0 48 21.5 48 48v64c0 26.5-21.5 48-48 48h-32c-8.8 0-16-7.2-16-16V368c0-8.8 7.2-16 16-16m32 128c8.8 0 16-7.2 16-16v-64c0-8.8-7.2-16-16-16h-16v96zm80-112c0-8.8 7.2-16 16-16h48c8.8 0 16 7.2 16 16s-7.2 16-16 16h-32v32h32c8.8 0 16 7.2 16 16s-7.2 16-16 16h-32v48c0 8.8-7.2 16-16 16s-16-7.2-16-16v-64z'/></svg>"
                ],
            ],
            "product" => [
                1 => [
                    "nb" => $nbProduct,
                    "link" => $this->generateUrl("back_app_user"),
                    "title" => "Products",
                    "icon" => "<svg xmlns='http://www.w3.org/2000/svg' class='w-full h-full' viewBox='0 0 24 24'><path fill='currentColor' d='m19.28 4.93l-2.12-2.12c-.78-.78-2.05-.78-2.83 0L11.5 5.64l2.12 2.12l2.12-2.12l3.54 3.54a3.012 3.012 0 0 0 0-4.25M5.49 13.77c.59.59 1.54.59 2.12 0l2.47-2.47l-2.12-2.13l-2.47 2.47c-.59.59-.59 1.54 0 2.13'/><path fill='currentColor' d='m15.04 7.76l-.71.71l-.71.71L10.44 6c-.59-.6-1.54-.6-2.12-.01a1.49 1.49 0 0 0 0 2.12l3.18 3.18l-.71.71l-6.36 6.36c-.78.78-.78 2.05 0 2.83c.78.78 2.05.78 2.83 0L16.45 12a.996.996 0 1 0 1.41-1.41z'/></svg>"
                ],
                2 => [
                    "nb" => $nbCategory,
                    "link" => $this->generateUrl("back_app_company"),
                    "title" => "Categories",
                    "icon" => "<svg xmlns='http://www.w3.org/2000/svg' class='w-full h-full' viewBox='0 0 24 24'><path fill='currentColor' d='M4 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5s1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5m0-6c-.83 0-1.5.67-1.5 1.5S3.17 7.5 4 7.5S5.5 6.83 5.5 6S4.83 4.5 4 4.5m0 12c-.83 0-1.5.68-1.5 1.5s.68 1.5 1.5 1.5s1.5-.68 1.5-1.5s-.67-1.5-1.5-1.5M7 19h14v-2H7zm0-6h14v-2H7zm0-8v2h14V5z'/></svg>"
                ],
            ]
        ];

        return $cards;
    }
}
