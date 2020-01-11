<?php

namespace App\Command;

use App\Service\ExportService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DataExportCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'data:export';

    /**
     * @var ExportService
     */
    protected $exportService;

    /**
     * Construct a new command.
     *
     * @param ExportService $exportService
     */
    public function __construct(ExportService $exportService)
    {
        parent::__construct();
        $this->exportService = $exportService;
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setDescription('Exports box and related data.')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Export format.  One of: json, xml, yaml.  Defaults to json.')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output filename.  Defaults to export-YYYYMMDDHHMM.{format}.')
            ->addOption('type',   't', InputOption::VALUE_REQUIRED, 'Export type.  One of: full, simple.  Defaults to full.');
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
        $io = new SymfonyStyle($input, $output);

        try {
            $format = $input->getOption('format') ?? 'json';
            $type   = $input->getOption('type')   ?? 'full';

            $file = $this->exportService->export([
                'format' => $format,
                'type'   => $type,
            ]);

            $output = $input->getOption('output') ?? $file->getSuggestedFilename();

            rename($file->getFilename(), $output);

            $io->success(sprintf('Wrote %s export to %s', $format, $output));
        } catch (\Exception $e) {
            $io->error(sprintf('Failed to export: %s', $e->getMessage()));
            return 1;
        }

        return 0;
    }
}
