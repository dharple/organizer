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
use App\Service\MoveService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console command to move boxes.
 */
class BoxMoveCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'box:move';

    /**
     * @var MoveService
     */
    protected $moveService;

    /**
     * Construct a new command.
     *
     * @param MoveService $moveService
     */
    public function __construct(MoveService $moveService)
    {
        parent::__construct();
        $this->moveService = $moveService;
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setDescription('Move boxes')
            ->addOption('box', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Box Number(s) to search on.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simulate the move in the database.')
            ->addOption('from', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Source Location ID.  At least one ID or from location must be specified.')
            ->addOption('id', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'ID(s) to move.')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Destination Location ID.');
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
        $io = new SymfonyStyle($input, $output);

        try {
            $table = new Table($output);
            $table->setRows(
                $this->moveService->move([
                    'box'      => $input->getOption('box'),
                    'dry-run'  => $input->getOption('dry-run'),
                    'from'     => $input->getOption('from'),
                    'id'       => $input->getOption('id'),
                    'to'       => $input->getOption('to'),
                ])
            );
            $table->render();
            if ($input->getOption('dry-run')) {
                $io->warning('Running in dry run mode.');
            } else {
                $io->success('Items moved.');
            }
        } catch (\Exception $e) {
            $io->error(sprintf('Failed to export: %s', $e->getMessage()));
            return 1;
        }

        return 0;
    }
}
