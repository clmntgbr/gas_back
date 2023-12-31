<?php

namespace App\Service;

use App\Entity\GasStation;

final class GasStationsMapService
{
    public function __construct(
        private array $lowGasPricesGasStationKey = ['keys' => [], 'gasPrice' => null, 'gasPriceId' => null],
    ) {
    }

    /** @param GasStation[] $gasStations */
    public function invoke(array $gasStations, string $gasTypeUuid): array
    {
        foreach ($gasStations as $key => $gasStation) {
            if (!array_key_exists($gasTypeUuid, $gasStation->getLastGasPrices())) {
                continue;
            }

            $gasPrice = $gasStation->getLastGasPrices()[$gasTypeUuid];

            if (empty($this->lowGasPricesGasStationKey['keys'])) {
                $this->lowGasPricesGasStationKey = ['keys' => [$key], 'gasPrice' => $gasPrice['gasPriceValue']];
                continue;
            }

            if ($this->lowGasPricesGasStationKey['gasPrice'] > $gasPrice['gasPriceValue']) {
                $this->lowGasPricesGasStationKey = ['keys' => [$key], 'gasPrice' => $gasPrice['gasPriceValue']];
                continue;
            }

            if ($this->lowGasPricesGasStationKey['gasPrice'] == $gasPrice['gasPriceValue']) {
                $this->lowGasPricesGasStationKey['keys'][] = $key;
            }
        }

        foreach ($this->lowGasPricesGasStationKey['keys'] as $value) {
            $gasStations[$value]->setHasLowPrices(true);
        }

        return $gasStations;
    }
}
