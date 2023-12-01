<?php

namespace App\MessageHandler;

use App\Message\UpdateGasStationClosedMessage;
use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler()]
final class UpdateGasStationClosedMessageHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private readonly GasStationRepository $gasStationRepository
    ) {
    }

    public function __invoke(UpdateGasStationClosedMessage $message): void
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $gasStation = $this->gasStationRepository->findOneBy(['gasStationId' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station is null (id: %s)', $message->getGasStationId()->getId()));
        }

        $this->em->persist($gasStation);
        $this->em->flush();
    }
}
