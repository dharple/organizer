<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Entity\Box;
use App\Entity\BoxModel;
use App\Entity\Location;
use App\Serializer\Normalizer\ExportContainerDenormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Moveer
 */
class MoveService
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructs a new Move service
     */
    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Moves from a file.
     */
    public function move(array $options)
    {
        $this->logger->info(json_encode($options));

        if (empty($options['box']) && empty($options['id']) && empty($options['from'])) {
            throw new \Exception('At least one Box ID, Box Number, or Source Location must be specified');
        }

        if (!isset($options['to'])) {
            throw new \Exception('A Destination Location must be specified');
        }

        $toLocation = $this->em->getRepository(Location::class)->find($options['to']);

        $query = [];
        if (!empty($options['box'])) {
            $query['boxNumber'] = $options['box'];
        }
        if (!empty($options['id'])) {
            $query['id'] = $options['id'];
        }
        if (!empty($options['from'])) {
            $query['location'] = $options['from'];
        }

        $boxes = $this->em->getRepository(Box::class)->findBy($query, ['boxNumber' => 'ASC']);

        $ret = [];
        foreach ($boxes as $box) {
            $ret[] = [
                'id' => $box->getId(),
                'label' => $box->getDisplayLabel(),
                'from' => $box->getLocation() ? $box->getLocation()->getDisplayLabel() : '~',
                'to' => $toLocation->getDisplayLabel(),
            ];

            if (!$options['dry-run']) {
                $box->setLocation($toLocation);
                $this->em->persist($box);
                $this->em->flush();
            }
        }

        return $ret;
    }
}
