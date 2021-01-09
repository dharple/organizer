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

use App\Service\ImportService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console command to import into the database.
 */
class DataImportCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'data:import';

    /**
     * @var ImportService
     */
    protected $importService;

    /**
     * Construct a new command.
     *
     * @param ImportService $importService
     */
    public function __construct(ImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setDescription('Imports box and related data.')
            ->addArgument('filename', InputArgument::REQUIRED, 'The file to import from.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simulate the import affecting the database.')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Export format.  One of: json, xml, yaml.  Defaults to the file\'s extension.');
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
            $this->importService->import([
                'dry-run'  => $input->getOption('dry-run'),
                'filename' => $input->getArgument('filename'),
                'format'   => $input->getOption('format'),
            ]);
        } catch (Exception $e) {
            $io->error(sprintf('Failed to export: %s', $e->getMessage()));
            return 1;
        }

        $io->success('File imported.');

        return 0;
    }
}
