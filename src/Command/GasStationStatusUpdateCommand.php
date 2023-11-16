<?php

namespace App\Command;

use App\Service\GasStationStatusUpdateCommandService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:gas-status:update',
)]
class GasStationStatusUpdateCommand extends Command
{
    public function __construct(
        private GasStationStatusUpdateCommandService $gasStationStatusUpdateCommandService,
    ) {
        parent::__construct(self::getDefaultName());
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'gasStationIds',
                InputArgument::IS_ARRAY,
                'Some GasStationIds ?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $gasStationIds = $input->getArgument('gasStationIds');

        $this->gasStationStatusUpdateCommandService->invoke($gasStationIds);

        return Command::SUCCESS;
    }
}
