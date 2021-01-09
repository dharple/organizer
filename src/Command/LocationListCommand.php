<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to list locations.
 */
class LocationListCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'location:list';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * Constructs a new command
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setDescription('List locations');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);
        $table->setHeaders(['id', 'label', 'boxes']);

        $locations = $this->em->getRepository(Location::class)->getSortedByDisplayLabel();
        foreach ($locations as $location) {
            $table->addRow([
                $location->getId(),
                $location->getDisplayLabel(),
                $location->getBoxes()->count(),
            ]);
        }

        $table->render();

        return 0;
    }
}
