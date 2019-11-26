<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    /**
     * @return Location[] Returns an array of Location objects
     */
    public function getSublocations(int $id)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.parentLocation = :id')
            ->orderBy('l.label', 'ASC')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Location[] Returns an array of Location objects
     */
    public function getTopLevelLocations()
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.parentLocation IS NULL')
            ->orderBy('l.label', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
