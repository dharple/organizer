<?php

namespace App\Repository;

use App\Entity\BoxModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BoxModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoxModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoxModel[]    findAll()
 * @method BoxModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoxModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoxModel::class);
    }

    // /**
    //  * @return BoxModel[] Returns an array of BoxModel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BoxModel
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
