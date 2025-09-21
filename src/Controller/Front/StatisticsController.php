<?php

namespace App\Controller\Front;

use App\Form\Front\AccountingStatsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Security('is_granted("ROLE_ACCOUNTANT")')]
#[Route('/statistics')]
class StatisticsController extends AbstractController
{
    #[Route('/', name: 'app_statistics_index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $isSuperAdmin = $this->isGranted('ROLE_SUPER_ADMIN');
        
        if ($isSuperAdmin) {
            // Pour le super admin, on récupère les stats de toutes les entreprises
            $company_id = null;
            $company = null;
            $creationYear = 2020; // Année de création par défaut pour les stats globales
        } else {
            // Pour les autres utilisateurs, on récupère les stats de leur entreprise
            if (!$user->getCompany()) {
                throw $this->createNotFoundException('Vous devez être associé à une entreprise pour accéder à cette fonctionnalité.');
            }
            $company_id = $user->getCompany()->getId();
            $company = $user->getCompany();
            $creationYear = $company->getCreatedAt()->format('Y');
        }

        $currentYear = (int) date('Y');
        $years = range($creationYear, $currentYear);

        $form = $this->createForm(AccountingStatsType::class, null, [
            'years' => array_combine($years, $years),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedYear = $form->get('year')->getData();
            return $this->redirectToRoute('front_app_statistics_index', ['year' => $selectedYear]);
        }

        $selectedYear = $request->query->getInt('year', date('Y'));

        $totalCustomers = $this->countEntitiesByDate($entityManager, 'App\Entity\Client', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalCategories = $this->countEntitiesByDate($entityManager, 'App\Entity\Category', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalProducts = $this->countEntitiesByDate($entityManager, 'App\Entity\Product', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalQuotations = $this->countEntitiesByDate($entityManager, 'App\Entity\Devis', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalPriceQuotations = $this->getTotalPriceOfQuotations($entityManager, new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalInvoices = $this->countEntitiesByDate($entityManager, 'App\Entity\Facture', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalPriceInvoices = $this->getTotalPriceOfQuotations($entityManager, new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);



        $hasStats = false;
        $stats = [];

        //dd($totalCustomers, $totalCategories, $totalProducts, $totalQuotations, $totalPriceQuotations, $totalInvoices, $totalPriceInvoices);

        if($totalCustomers>0 && $totalCategories>0 && $totalProducts>0 && $totalQuotations>0 && $totalPriceQuotations>0 && $totalPriceInvoices>0 && $totalPriceInvoices>0){
            $hasStats = true;
            for ($month = 1; $month <= 12; $month++) {
                $lastDayOfMonth = (int) date('t', strtotime("$selectedYear-$month-01"));

                $startDate = new \DateTime("$selectedYear-$month-01 00:00:00");
                $endDate = new \DateTime("$selectedYear-$month-$lastDayOfMonth 23:59:59");

                $customerCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Client', $startDate, $endDate, $company_id);
                $categoryCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Category', $startDate, $endDate, $company_id);
                $productCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Product', $startDate, $endDate, $company_id);
                $quotationsCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Devis', $startDate, $endDate, $company_id);
                $invoicesCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Facture', $startDate, $endDate, $company_id);
                $totalPriceMonth = $this->getTotalPriceOfQuotations($entityManager, $startDate, $endDate, $company_id);

                $monthNames = [
                    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                    5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                    9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                ];
                
                $stats[$month] = [
                    'month' => $monthNames[$month],
                    'customer' => [
                        'total' => $customerCount,
                        'percent' => number_format($totalCustomers > 0 ? ($customerCount / $totalCustomers) * 100 : 0, 2),
                    ],
                    'category' => [
                        'total' => $categoryCount,
                        'percent' => number_format($totalCategories > 0 ? ($categoryCount / $totalCategories) * 100 : 0, 2),
                    ],
                    'product' => [
                        'total' => $productCount,
                        'percent' => number_format($totalProducts > 0 ? ($productCount / $totalProducts) * 100 : 0, 2),
                    ],
                    'quotations' => [
                        'total' => $quotationsCount,
                        'total_price' => number_format($totalPriceMonth, 2),
                        'percent' => number_format($totalQuotations > 0 ? ($quotationsCount / $totalQuotations) * 100 : 0, 2),
                        'percent_total_price' => number_format($totalPriceQuotations > 0 ? ($totalPriceMonth / $totalPriceQuotations) * 100 : 0, 2),
                    ],
                    'invoices' => [
                        'total' => $invoicesCount,
                        'total_price' => number_format($totalPriceMonth, 2),
                        'percent' => number_format($totalInvoices > 0 ? ($invoicesCount / $totalInvoices) * 100 : 0, 2),
                        'percent_total_price' => number_format($totalPriceInvoices > 0 ? ($totalPriceMonth / $totalPriceInvoices) * 100 : 0, 2),
                    ],
                ];
            }

            $totalPriceAllQuotations = $this->getTotalPriceOfQuotations($entityManager, new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"),$company_id);
            $totalPriceAllInvoices = $this->getTotalPriceOfInvoices($entityManager, new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"),$company_id);

            $summary = [
                'customer' => [
                    'total' => $totalCustomers,
                ],
                'category' => [
                    'total' => $totalCategories,
                ],
                'product' => [
                    'total' => $totalProducts,
                ],
                'quotations' => [
                    'total' => $totalQuotations,
                    'total_price' => number_format($totalPriceAllQuotations, 2),
                ],
                'invoices' => [
                    'total' => $totalInvoices,
                    'total_price' => number_format($totalPriceAllInvoices, 2),
                ],
            ];

            $months = [];

            $monthNames = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            ];
            
            for ($month = 1; $month <= 12; $month++) {
                $months[$month] = $monthNames[$month];
            }

            return $this->render('/front/statistics/index.html.twig', [
                'form' => $form->createView(),
                'stats' => $stats,
                'summary' => $summary,
                'controller_name' => 'StatisticsController',
                'months' => $months,
                'hasStats' => true,
                'isSuperAdmin' => $isSuperAdmin,
                'company' => $company,
            ]);
        }


        return $this->render('/front/statistics/index.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'StatisticsController',
            'hasStats' => false,
            'isSuperAdmin' => $isSuperAdmin,
            'company' => $company,
        ]);
    }

    #[Route('/export-csv', name: 'app_statistics_export_csv')]
    public function exportCsv(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $isSuperAdmin = $this->isGranted('ROLE_SUPER_ADMIN');
        
        if ($isSuperAdmin) {
            // Pour le super admin, on récupère les stats de toutes les entreprises
            $company_id = null;
            $company = null;
        } else {
            // Pour les autres utilisateurs, on récupère les stats de leur entreprise
            if (!$user || !$user->getCompany()) {
                throw $this->createNotFoundException('Vous devez être connecté et avoir une société pour accéder à cette fonctionnalité.');
            }
            $company_id = $user->getCompany()->getId();
            $company = $user->getCompany();
        }
        
        $selectedYear = $request->query->getInt('year', date('Y'));
        
        // Récupération des données annuelles
        $totalCustomers = $this->countEntitiesByDate($entityManager, 'App\Entity\Client', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalCategories = $this->countEntitiesByDate($entityManager, 'App\Entity\Category', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalProducts = $this->countEntitiesByDate($entityManager, 'App\Entity\Product', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalQuotations = $this->countEntitiesByDate($entityManager, 'App\Entity\Devis', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalPriceQuotations = $this->getTotalPriceOfQuotations($entityManager, new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalInvoices = $this->countEntitiesByDate($entityManager, 'App\Entity\Facture', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalPriceInvoices = $this->getTotalPriceOfInvoices($entityManager, new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);

        // Création du contenu CSV avec BOM UTF-8 pour Excel
        $csvContent = "\xEF\xBB\xBF"; // BOM UTF-8
        $companyName = $isSuperAdmin ? "Toutes les entreprises" : $company->getName();
        $csvContent .= "Statistiques " . $companyName . " - Année $selectedYear\n\n";
        
        // Résumé annuel
        $csvContent .= "RÉSUMÉ ANNUEL $selectedYear\n";
        $csvContent .= "Catégorie;Total;Prix Total\n";
        $csvContent .= "Clients;$totalCustomers;-\n";
        $csvContent .= "Catégories;$totalCategories;-\n";
        $csvContent .= "Produits;$totalProducts;-\n";
        $csvContent .= "Devis;$totalQuotations;" . number_format($totalPriceQuotations, 2, ',', ' ') . " €\n";
        $csvContent .= "Factures;$totalInvoices;" . number_format($totalPriceInvoices, 2, ',', ' ') . " €\n\n";
        
        // Détail par mois
        $csvContent .= "DÉTAIL MENSUEL $selectedYear\n";
        $csvContent .= "Mois;Clients Total;Clients %;Catégories Total;Catégories %;Produits Total;Produits %;Devis Total;Devis Prix;Devis %;Factures Total;Factures Prix;Factures %\n";
        
        for ($month = 1; $month <= 12; $month++) {
            $lastDayOfMonth = (int) date('t', strtotime("$selectedYear-$month-01"));
            $startDate = new \DateTime("$selectedYear-$month-01 00:00:00");
            $endDate = new \DateTime("$selectedYear-$month-$lastDayOfMonth 23:59:59");
            
            $customerCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Client', $startDate, $endDate, $company_id);
            $categoryCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Category', $startDate, $endDate, $company_id);
            $productCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Product', $startDate, $endDate, $company_id);
            $quotationsCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Devis', $startDate, $endDate, $company_id);
            $invoicesCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Facture', $startDate, $endDate, $company_id);
            $totalPriceQuotationsMonth = $this->getTotalPriceOfQuotations($entityManager, $startDate, $endDate, $company_id);
            $totalPriceInvoicesMonth = $this->getTotalPriceOfInvoices($entityManager, $startDate, $endDate, $company_id);
            
            $customerPercent = $totalCustomers > 0 ? number_format(($customerCount / $totalCustomers) * 100, 2, ',', ' ') : '0,00';
            $categoryPercent = $totalCategories > 0 ? number_format(($categoryCount / $totalCategories) * 100, 2, ',', ' ') : '0,00';
            $productPercent = $totalProducts > 0 ? number_format(($productCount / $totalProducts) * 100, 2, ',', ' ') : '0,00';
            $quotationPercent = $totalQuotations > 0 ? number_format(($quotationsCount / $totalQuotations) * 100, 2, ',', ' ') : '0,00';
            $invoicePercent = $totalInvoices > 0 ? number_format(($invoicesCount / $totalInvoices) * 100, 2, ',', ' ') : '0,00';
            
            $monthNames = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            ];
            $monthName = $monthNames[$month];
            
            $csvContent .= "$monthName;$customerCount;$customerPercent%;$categoryCount;$categoryPercent%;$productCount;$productPercent%;$quotationsCount;" . number_format($totalPriceQuotationsMonth, 2, ',', ' ') . " €;$quotationPercent%;$invoicesCount;" . number_format($totalPriceInvoicesMonth, 2, ',', ' ') . " €;$invoicePercent%\n";
        }

        // Création de la réponse avec le fichier CSV
        $response = new Response($csvContent);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8; separator=;');
        $fileName = $isSuperAdmin ? "statistiques_globales" : "statistiques_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $company->getName());
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '_' . $selectedYear . '.csv"');
        
        return $response;
    }

    #[Route('/export-pdf', name: 'app_statistics_export_pdf')]
    public function exportPdf(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $isSuperAdmin = $this->isGranted('ROLE_SUPER_ADMIN');
        
        if ($isSuperAdmin) {
            // Pour le super admin, on récupère les stats de toutes les entreprises
            $company_id = null;
            $company = null;
        } else {
            // Pour les autres utilisateurs, on récupère les stats de leur entreprise
            if (!$user || !$user->getCompany()) {
                throw $this->createNotFoundException('Vous devez être connecté et avoir une société pour accéder à cette fonctionnalité.');
            }
            $company_id = $user->getCompany()->getId();
            $company = $user->getCompany();
        }
        $selectedYear = $request->query->getInt('year', date('Y'));
        
        // Récupération des données annuelles
        $totalCustomers = $this->countEntitiesByDate($entityManager, 'App\Entity\Client', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalCategories = $this->countEntitiesByDate($entityManager, 'App\Entity\Category', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalProducts = $this->countEntitiesByDate($entityManager, 'App\Entity\Product', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalQuotations = $this->countEntitiesByDate($entityManager, 'App\Entity\Devis', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalPriceQuotations = $this->getTotalPriceOfQuotations($entityManager, new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalInvoices = $this->countEntitiesByDate($entityManager, 'App\Entity\Facture', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);
        $totalPriceInvoices = $this->getTotalPriceOfInvoices($entityManager, new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"), $company_id);

        // Génération des données par mois
        $stats = [];
        for ($month = 1; $month <= 12; $month++) {
            $lastDayOfMonth = (int) date('t', strtotime("$selectedYear-$month-01"));
            $startDate = new \DateTime("$selectedYear-$month-01 00:00:00");
            $endDate = new \DateTime("$selectedYear-$month-$lastDayOfMonth 23:59:59");
            
            $customerCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Client', $startDate, $endDate, $company_id);
            $categoryCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Category', $startDate, $endDate, $company_id);
            $productCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Product', $startDate, $endDate, $company_id);
            $quotationsCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Devis', $startDate, $endDate, $company_id);
            $invoicesCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Facture', $startDate, $endDate, $company_id);
            $totalPriceQuotationsMonth = $this->getTotalPriceOfQuotations($entityManager, $startDate, $endDate, $company_id);
            $totalPriceInvoicesMonth = $this->getTotalPriceOfInvoices($entityManager, $startDate, $endDate, $company_id);
            
            $monthNames = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            ];
            
            $stats[$month] = [
                'month' => $monthNames[$month],
                'customer' => [
                    'total' => $customerCount,
                    'percent' => number_format($totalCustomers > 0 ? ($customerCount / $totalCustomers) * 100 : 0, 2),
                ],
                'category' => [
                    'total' => $categoryCount,
                    'percent' => number_format($totalCategories > 0 ? ($categoryCount / $totalCategories) * 100 : 0, 2),
                ],
                'product' => [
                    'total' => $productCount,
                    'percent' => number_format($totalProducts > 0 ? ($productCount / $totalProducts) * 100 : 0, 2),
                ],
                'quotations' => [
                    'total' => $quotationsCount,
                    'total_price' => number_format($totalPriceQuotationsMonth, 2),
                    'percent' => number_format($totalQuotations > 0 ? ($quotationsCount / $totalQuotations) * 100 : 0, 2),
                    'percent_total_price' => number_format($totalPriceQuotations > 0 ? ($totalPriceQuotationsMonth / $totalPriceQuotations) * 100 : 0, 2),
                ],
                'invoices' => [
                    'total' => $invoicesCount,
                    'total_price' => number_format($totalPriceInvoicesMonth, 2),
                    'percent' => number_format($totalInvoices > 0 ? ($invoicesCount / $totalInvoices) * 100 : 0, 2),
                    'percent_total_price' => number_format($totalPriceInvoices > 0 ? ($totalPriceInvoicesMonth / $totalPriceInvoices) * 100 : 0, 2),
                ],
            ];
        }

        $summary = [
            'customer' => ['total' => $totalCustomers],
            'category' => ['total' => $totalCategories],
            'product' => ['total' => $totalProducts],
            'quotations' => [
                'total' => $totalQuotations,
                'total_price' => number_format($totalPriceQuotations, 2),
            ],
            'invoices' => [
                'total' => $totalInvoices,
                'total_price' => number_format($totalPriceInvoices, 2),
            ],
        ];

        // Configuration PDF (même logique que dans vos autres contrôleurs)
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        // Rendu du template HTML pour PDF
        $html = $this->renderView('front/statistics/pdf_statistics_template.html.twig', [
            'company' => $company,
            'isSuperAdmin' => $isSuperAdmin,
            'selectedYear' => $selectedYear,
            'stats' => $stats,
            'summary' => $summary,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Nom de fichier sécurisé
        $pdfFileName = $isSuperAdmin ? "statistiques_globales_" . $selectedYear . ".pdf" : "statistiques_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $company->getName()) . "_" . $selectedYear . ".pdf";

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$pdfFileName.'"',
        ]);
    }

    private function countEntitiesByDate(EntityManagerInterface $entityManager, $entityClass, $startDate, $endDate, $company_id)
    {
        $qb = $entityManager->createQueryBuilder();

        $classMetadata = $entityManager->getClassMetadata($entityClass);
        $className = $classMetadata->getName();

        $qb->select('COUNT(e.id)')
            ->from($className, 'e')
            ->where($qb->expr()->between('e.createdAt', ':start', ':end'))
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);

        // Si company_id est fourni, on filtre par entreprise
        if ($company_id !== null) {
            $qb->andWhere($qb->expr()->eq('e.company_id', ':company_id'))
               ->setParameter('company_id', $company_id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    private function getTotalPriceOfQuotations(EntityManagerInterface $entityManager, $startDate, $endDate, $company_id)
    {
        $qb = $entityManager->createQueryBuilder();

        $qb->select('SUM(e.total_price)')
            ->from('App\Entity\Devis', 'e')
            ->where($qb->expr()->between('e.createdAt', ':start', ':end'))
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);

        // Si company_id est fourni, on filtre par entreprise
        if ($company_id !== null) {
            $qb->andWhere($qb->expr()->eq('e.company_id', ':company_id'))
               ->setParameter('company_id', $company_id);
        }

        return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }

    private function getTotalPriceOfInvoices(EntityManagerInterface $entityManager, $startDate, $endDate, $company_id)
    {
        $qb = $entityManager->createQueryBuilder();

        $qb->select('SUM(e.prix_total)')
            ->from('App\Entity\Facture', 'e')
            ->where($qb->expr()->between('e.createdAt', ':start', ':end'))
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);

        // Si company_id est fourni, on filtre par entreprise
        if ($company_id !== null) {
            $qb->andWhere($qb->expr()->eq('e.company_id', ':company_id'))
               ->setParameter('company_id', $company_id);
        }

        return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }
}
