<?php

namespace App\Controller\Front;

use App\Form\Front\AccountingStatsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

#[Security('is_granted("ROLE_ACCOUNTANT"')]
#[Route('/statistics')]
class StatisticsController extends AbstractController
{
    #[Route('/', name: 'app_statistics_index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $company = $user->getCompany();

        $creationYear = $company->getCreatedAt()->format('Y');

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

        $totalCustomers = $this->countEntitiesByDate($entityManager, 'App\Entity\Client', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"));
        $totalCategories = $this->countEntitiesByDate($entityManager, 'App\Entity\Category', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"));
        $totalProducts = $this->countEntitiesByDate($entityManager, 'App\Entity\Product', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"));
        $totalQuotations = $this->countEntitiesByDate($entityManager, 'App\Entity\Devis', new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"));
        $totalPriceQuotations = $this->getTotalPriceOfQuotations($entityManager, new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"));

        $hasStats = false;
        $stats = [];

        if($totalCustomers>0 && $totalCategories>0 && $totalProducts>0 && $totalQuotations>0 && $totalPriceQuotations>0){
            $hasStats = true;
            for ($month = 1; $month <= 12; $month++) {
                $lastDayOfMonth = (int) date('t', strtotime("$selectedYear-$month-01"));

                $startDate = new \DateTime("$selectedYear-$month-01 00:00:00");
                $endDate = new \DateTime("$selectedYear-$month-$lastDayOfMonth 23:59:59");

                $customerCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Client', $startDate, $endDate);
                $categoryCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Category', $startDate, $endDate);
                $productCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Product', $startDate, $endDate);
                $quotationsCount = $this->countEntitiesByDate($entityManager, 'App\Entity\Devis', $startDate, $endDate);
                $totalPriceMonth = $this->getTotalPriceOfQuotations($entityManager, $startDate, $endDate);

                $stats[$month] = [
                    'month' => date('F', mktime(0, 0, 0, $month, 1)),
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
                ];
            }

            $totalPriceAllQuotations = $this->getTotalPriceOfQuotations($entityManager, new \DateTime("$selectedYear-01-01 00:00:00"), new \DateTime("$selectedYear-12-31 23:59:59"));

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
                    'total_price' => number_format($totalPriceAllQuotations, 2), // Format total_price with 2 decimals
                ],
            ];

            $months = [];

            for ($month = 1; $month <= 12; $month++) {
                $months[$month] = date('F', mktime(0, 0, 0, $month, 1));
            }

            return $this->render('/front/statistics/index.html.twig', [
                'form' => $form->createView(),
                'stats' => $stats,
                'summary' => $summary,
                'controller_name' => 'StatisticsController',
                'months' => $months,
                'hasStats' => true,
            ]);
        }


        return $this->render('/front/statistics/index.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'StatisticsController',
            'hasStats' => false,
        ]);
    }

    private function countEntitiesByDate(EntityManagerInterface $entityManager, $entityClass, $startDate, $endDate)
    {
        $qb = $entityManager->createQueryBuilder();

        $classMetadata = $entityManager->getClassMetadata($entityClass);
        $className = $classMetadata->getName();

        $qb->select('COUNT(e.id)')
            ->from($className, 'e')
            ->where($qb->expr()->between('e.createdAt', ':start', ':end'))
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);

        return $qb->getQuery()->getSingleScalarResult();
    }

    private function getTotalPriceOfQuotations(EntityManagerInterface $entityManager, $startDate, $endDate)
    {
        $qb = $entityManager->createQueryBuilder();

        $qb->select('SUM(q.total_price)')
            ->from('App\Entity\Devis', 'q')
            ->where($qb->expr()->between('q.createdAt', ':start', ':end'))
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);

        return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }
}
