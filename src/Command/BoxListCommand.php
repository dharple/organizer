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

use App\Entity\Box;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to list boxes.
 */
class BoxListCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'box:list';

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
        $this
            ->setDescription('List boxes')
            ->addOption('box', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Box Number(s) to search on.')
            ->addOption('id', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Box ID(s) to search on.')
            ->addOption('location', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Location ID(s) to search on.')
        ;
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
        $table->setHeaders(['id', 'label', 'location']);

        $query = [];
        $boxNumbers = $input->getOption('box');
        if (!empty($boxNumbers)) {
            $query['boxNumber'] = $boxNumbers;
        }
        $ids = $input->getOption('id');
        if (!empty($ids)) {
            $query['id'] = $ids;
        }
        $locations = $input->getOption('location');
        if (!empty($locations)) {
            $query['location'] = $locations;
        }

        $boxes = $this->em->getRepository(Box::class)->findBy($query, ['boxNumber' => 'ASC']);
        foreach ($boxes as $box) {
            $table->addRow([
                $box->getId(),
                $box->getDisplayLabel(),
                $box->getLocation() ? $box->getLocation()->getDisplayLabel() : '~',
            ]);
        }

        $table->render();

        return 0;
    }
}
