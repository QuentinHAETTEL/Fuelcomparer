<?php

namespace App\Command;

use App\Service\PricesImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-prices'
)]
class ImportPricesCommand extends Command
{
    public function __construct(private PricesImporter $pricesImporter, string $name = null)
    {
        parent::__construct($name);
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->pricesImporter->import($io)) {
            $io->success('Import complete');
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}