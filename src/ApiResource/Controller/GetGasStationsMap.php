<?php

namespace App\ApiResource\Controller;

use App\Repository\GasStationRepository;
use App\Service\GasStationsMapService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetGasStationsMap extends AbstractController
{
    public static string $operationName = 'get_gas_stations_map';

    public function __construct(
        private readonly GasStationRepository $gasStationRepository,
        private readonly GasStationsMapService $gasStationsMapService,
        private readonly string $latitudeDefault,
        private readonly string $longitudeDefault,
        private readonly string $gasTypeUuidDefault,
        private readonly string $radiusDefault
    ) {
    }

    public function __invoke(Request $request): array
    {
        $latitude = $request->query->get('latitude') ?? $this->latitudeDefault;
        $longitude = $request->query->get('longitude') ?? $this->longitudeDefault;
        $radius = $request->query->get('radius') ?? $this->radiusDefault;
        $gasTypeUuid = $request->query->get('gas_type_uuid') ?? $this->gasTypeUuidDefault;
        $filterCity = $request->query->get('filter_city') ?? null;
        $filterDepartment = $request->query->get('filter_department') ?? null;

        $gasStations = $this->gasStationRepository->getGasStationsMap($longitude, $latitude, $gasTypeUuid, $radius/2, $filterCity, $filterDepartment);
        return $this->gasStationsMapService->invoke($gasStations, $gasTypeUuid);
    }
}
