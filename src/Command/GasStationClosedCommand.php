<?php

namespace App\Command;

use App\Service\GasStationCloseCommandService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:gas-station:close',
)]
class GasStationClosedCommand extends Command
{
    public function __construct(
        private GasStationClosedCommandService $gasStationClosedCommandService,
    ) {
        parent::__construct(self::getDefaultName());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->gasStationClosedCommandService->invoke();

        return Command::SUCCESS;
    }
}
