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
     * Finds any locations whose display label contains the passed string.
     * Label is case-insensitive.
     *
     * @param string $label The label to search for.
     *
     * @return Location[]
     */
    public function findByDisplayLabel(string $label): array
    {
        $ret = [];
        $all = $this->findAll();
        foreach ($all as $location) {
            if (stripos($location->getDisplayLabel(), $label) === false) {
                continue;
            }

            $ret[] = $location;
        }

        return $ret;
    }

    /**
     * Sorted by displayLabel
     *
     * @return Location[] Returns an array of Location objects
     */
    public function getSorted()
    {
        $ret = $this->findAll();
        usort($ret, '\App\Utility\Sort::sortByDisplayLabel');
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
        usort($ret, '\App\Utility\Sort::sortByDisplayLabel');
        return $ret;
    }

    /**
     * @return Location[] Returns an array of Location objects
     */
    public function getSublocations(int $id)
    {
        return $this->findBy(['parentLocation' => $id], ['label' => 'ASC']);
    }

    /**
     * @return Location[] Returns an array of Location objects
     */
    public function getTopLevelLocations()
    {
        return $this->findBy(['parentLocation' => null], ['label' => 'ASC']);
    }
}
