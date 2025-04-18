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
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
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
     * How much weight to add when dealing with an exact match.
     *
     * @var integer
     */
    protected const EXACT_MATCH_WEIGHT = 10;

    /**
     * Constructor
     */
    public function __construct(
        ManagerRegistry $registry,
        protected LoggerInterface $logger,
        protected LocationRepository $locationRepository
    ) {
        parent::__construct($registry, Box::class);
    }

    /**
     * Simple keyword search
     */
    public function findByKeyword($keyword)
    {
        $single = true;
        $keywords = preg_split('/[\s,]+/', trim((string) $keyword));
        if ((is_countable($keywords) ? count($keywords) : 0) > 1) {
            $single = false;
            array_push($keywords, $keyword);
        }

        $this->logger->info('keywords: ' . json_encode($keywords, JSON_PARTIAL_OUTPUT_ON_ERROR));

        $output = [];
        $numeric = false;
        foreach ($keywords as $keyword) {
            if (is_numeric($keyword)) {
                $output = array_merge(
                    $output,
                    $this->findBy(['boxNumber' => ltrim($keyword, '0')])
                );
                $numeric = true;
            }

            $locations = $this->locationRepository->findByDisplayLabel($keyword);
            $output = array_merge(
                $output,
                $this->createQueryBuilder('b')
                    ->orWhere('IDENTITY(b.location) IN (?2)')
                    ->orWhere('IDENTITY(b.boxModel) IN (SELECT DISTINCT m.id FROM App\Entity\BoxModel m WHERE m.label LIKE ?1)')
                    ->orWhere('b.label LIKE ?1')
                    ->orWhere('b.description LIKE ?1')
                    ->setParameter('1', '%' . $keyword . '%')
                    ->setParameter('2', $locations)
                    ->getQuery()
                    ->getResult()
            );
        }

        $all = [];
        $counts = [];
        foreach ($output as $box) {
            $exactMatch = ($single == true && $numeric == true && $box->getBoxNumber() == $keyword);

            if ($box->isHidden() && !$exactMatch) {
                continue;
            }

            $id = $box->getBoxNumber();
            if (!isset($counts[$id])) {
                $all[$id] = $box;
                $counts[$id] = 1;
            } else {
                $counts[$id]++;
            }

            if ($exactMatch) {
                $counts[$id] += static::EXACT_MATCH_WEIGHT;
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
        $this->logger->info('search results: ' . json_encode($counts, JSON_PARTIAL_OUTPUT_ON_ERROR));
        $ret = [];
        foreach ($counts as $id => $count) {
            $ret[] = $all[$id];
        }

        return $ret;
    }

    /**
     * Get next box number.
     */
    public function getNextBoxNumber(): int
    {
        $this->getEntityManager()->getFilters()->disable('SoftDeleteable');
        $box = $this->findOneBy([], ['boxNumber' => 'DESC']);
        $this->getEntityManager()->getFilters()->enable('SoftDeleteable');
        return is_object($box) ? $box->getBoxNumber() + 1 : 1;
    }

    /**
     * Recently updated
     *
     * @param string  $recent time string
     * @param integer $limit
     *
     * @return AbstractLazyCollection Returns a collection of Box objects
     *
     * @throws Exception
     */
    public function getRecent($recent = '-30 days', $limit = null)
    {
        return $this->matching(
            Criteria::create()
                ->where(Criteria::expr()->gt('updatedAt', new DateTime($recent)))
                ->orderBy(['updatedAt' => Criteria::DESC])
                ->setMaxResults($limit)
        );
    }

    /**
     * Sorted by display label
     *
     * @return Box[] Returns an array of Box objects
     */
    public function getSortedByDisplayLabel()
    {
        return $this->findBy([], ['boxNumber' => 'ASC']);
    }

    /**
     * Sorted by id
     *
     * @return Box[] Returns an array of Box objects
     */
    public function getSortedById()
    {
        return $this->findBy([], ['id' => 'ASC']);
    }
}
