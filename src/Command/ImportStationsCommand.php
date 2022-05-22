<?php

namespace App\Command;

use App\Service\StationsImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-stations',
)]
class ImportStationsCommand extends Command
{
    public function __construct(private StationsImporter $stationsImporter, string $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->stationsImporter->import($io)) {
            $io->success('Import complete');
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
