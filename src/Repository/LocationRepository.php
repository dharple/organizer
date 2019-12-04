<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    /**
     * Constructor
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    /**
     * Sorted by displayLabel
     *
     * @return Location[] Returns an array of Location objects
     */
    public function getSorted()
    {
        $ret = $this->createQueryBuilder('l')
            ->getQuery()
            ->getResult();

        usort($ret, [$this, 'sortByDisplayLabel']);

        return $ret;
    }

    /**
     * @return Location[] Returns an array of Location objects
     */
    public function getSortedWithBoxes()
    {
        $ret = $this->createQueryBuilder('l')
            ->where('l.id IN (SELECT DISTINCT IDENTITY(b.location) FROM App\Entity\Box b)')
            ->getQuery()
            ->getResult();

        usort($ret, [$this, 'sortByDisplayLabel']);

        return $ret;
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

    /**
     * Method for sorting locations by display label
     */
    public function sortByDisplayLabel(Location $a, Location $b)
    {
        $a = $a->getDisplayLabel();
        $b = $b->getDisplayLabel();
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }
}
