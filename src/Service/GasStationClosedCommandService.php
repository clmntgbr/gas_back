<?php

namespace App\Service;

use App\Entity\EntityId\GasStationId;
use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use App\Message\UpdateGasStationClosedMessage;
use App\Repository\GasStationRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class GasStationClosedCommandService
{
    public function __construct(
        private GasStationRepository $gasStationRepository,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function invoke()
    {
        $gasStations = $this->gasStationRepository->findGasStationsExceptByStatus(GasStationStatusReference::CLOSED);

        foreach ($gasStations as $gasStation) {
            if (null === $gasStation->getLastGasPrices()) {
                return $this->sendTo($gasStation);
            }
        }
    }

    private function sendTo(GasStation $gasStation)
    {
        return $this->messageBus->dispatch(new UpdateGasStationClosedMessage(new GasStationId($gasStation->getGasStationId())));
    }
}