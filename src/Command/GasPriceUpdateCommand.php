<?php

namespace App\Command;

use App\Service\GasPriceUpdateCommandService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:gas-price:update',
)]
class GasPriceUpdateCommand extends Command
{
    public function __construct(
        private GasPriceUpdateCommandService $gasPriceUpdateCommandService,
    ) {
        parent::__construct(self::getDefaultName());
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->gasPriceUpdateCommandService->invoke();

        return Command::SUCCESS;
    }
}
