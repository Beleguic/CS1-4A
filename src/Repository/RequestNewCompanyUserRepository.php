<?php

namespace App\Repository;

use App\Entity\RequestNewCompanyUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RequestNewCompanyUser>
 *
 * @method RequestNewCompanyUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestNewCompanyUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestNewCompanyUser[]    findAll()
 * @method RequestNewCompanyUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestNewCompanyUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestNewCompanyUser::class);
    }

    //    /**
    //     * @return RequestNewCompanyUser[] Returns an array of RequestNewCompanyUser objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RequestNewCompanyUser
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
