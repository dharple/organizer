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

use App\Entity\Box;
use App\Repository\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @method Box|null find($id, $lockMode = null, $lockVersion = null)
 * @method Box|null findOneBy(array $criteria, array $orderBy = null)
 * @method Box[]    findAll()
 * @method Box[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoxRepository extends ServiceEntityRepository
{
    /**
     * @var LocationRepository
     */
    protected $locationRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     */
    public function __construct(
        ManagerRegistry $registry,
        LoggerInterface $logger,
        LocationRepository $locationRepository
    ) {
        parent::__construct($registry, Box::class);

        $this->locationRepository = $locationRepository;
        $this->logger             = $logger;
    }

    /**
     * Simple keyword search
     */
    public function findByKeyword($keyword)
    {
        $all = [];
        $counts = [];

        $keywords = preg_split('/[\s,]+/', trim($keyword));
        $this->logger->info('keywords: ' . json_encode($keywords));
        foreach ($keywords as $keyword) {
            $output = [];
            if (is_numeric($keyword)) {
                $output = $this->findBy(['id' => ltrim($keyword, '0')]);
            } else {
                $locations = $this->locationRepository->findByDisplayLabel($keyword);
                $output = $this->createQueryBuilder('b')
                    ->orWhere('IDENTITY(b.location) IN (?2)')
                    ->orWhere('IDENTITY(b.boxModel) IN (SELECT DISTINCT m.id FROM App\Entity\BoxModel m WHERE m.label LIKE ?1)')
                    ->orWhere('b.label LIKE ?1')
                    ->orWhere('b.description LIKE ?1')
                    ->setParameter('1', '%' . $keyword . '%')
                    ->setParameter('2', $locations)
                    ->getQuery()
                    ->getResult();
            }

            foreach ($output as $box) {
                $id = $box->getId();
                $all[$id] = $box;
                if (!isset($counts[$id])) {
                    $counts[$id] = 1;
                } else {
                    $counts[$id]++;
                }
            }
        }

        //
        // Update counts so that we sort first by number of hits DESC and then
        // box number ASC.  We have to flip one of the numbers, so I'm choosing
        // to flip the count.  If there are more than 100 keywords in the
        // search, this breaks.
        //
        array_walk(
            $counts,
            function (&$value, $key) {
                $value = sprintf('%d%04d', 100 - $value, $key);
            }
        );
        asort($counts);
        $this->logger->info('search results: ' . json_encode($counts));
        $ret = [];
        foreach ($counts as $id => $count) {
            $ret[] = $all[$id];
        }

        return $ret;
    }

    /**
     * Sorted by id
     *
     * @return Box[] Returns an array of Box objects
     */
    public function getSorted()
    {
        return $this->findBy([], ['id' => 'ASC']);
    }
}
