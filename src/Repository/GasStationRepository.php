<?php

namespace App\Repository;

use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GasStation>
 *
 * @method GasStation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasStation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasStation[]    findAll()
 * @method GasStation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GasStationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GasStation::class);
    }

    public function findGasStationsById(): array
    {
        $query = $this->createQueryBuilder('s')
            ->indexBy('s', 's.gasStationId')
            ->select('s.gasStationId, s.hash')
            ->getQuery();

        return $query->getArrayResult();
    }

    public function findGasStationsByStatus(string $status): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.status = :status')
            ->setParameter('status', $status)
            ->getQuery();

        return $query->getResult();
    }

    /** @return GasStation[] */
    public function findGasStationsExceptByStatus(string $status)
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.status != :status')
            ->setParameter('status', $status)
            ->getQuery();

        return $query->getResult();
    }

    /** @return GasStation[] */
    public function findGasStationByIds(array $gasStationIds)
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.gasStationId IN (:ids)')
            ->setParameter('ids', $gasStationIds)
            ->getQuery();

        return $query->getResult();
    }

    public function findGasStationsByPlaceIdNotNull(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.googlePlace', 'ss')
            ->where('ss.placeId IS NOT NULL')
            ->getQuery();

        return $query->getResult();
    }

    public function findGasStationByPlaceIdAndStatus(string $placeId, string $status): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.googlePlace', 'ss')
            ->where('ss.placeId = :placeId AND s.status = :status')
            ->setParameters(
                ['status' => $status, 'placeId' => $placeId]
            )
            ->getQuery();

        return $query->getResult();
    }

    public function findGasStationsByPlaceId(string $placeId): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.googlePlace', 'ss')
            ->where('ss.placeId = :placeId')
            ->setParameters(
                ['placeId' => $placeId]
            )
            ->getQuery();

        return $query->getResult();
    }

    public function findRandomGasStation(): ?GasStation
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.status IN (:status)')
            ->setParameter('status',[GasStationStatusReference::WAITING_VALIDATION, GasStationStatusReference::PLACE_ID_ANOMALY])
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /** @return GasStation[] */
    public function getGasStationsMap(string $longitude, string $latitude, string $gasTypeUuid, string $radius, ?string $filterCity, ?string $filterDepartment)
    {
        $gasTypeFilter = $this->createGasTypeFilter($gasTypeUuid);
        $cityFilter = $this->createGasStationsCitiesFilter($filterCity);
        $departmentFilter = $this->createGasStationsDepartmentsFilter($filterDepartment);

        $query = "  SELECT 
                    s.id, 
                    ((6371 * acos(cos(radians($latitude)) * cos(radians(a.latitude)) * cos(radians(a.longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(a.latitude))))*1000) as distance,
                    
                    JSON_KEYS(s.last_gas_prices) as gas_types,
                    
                    (SELECT GROUP_CONCAT(gs.name SEPARATOR ', ')
                    FROM gas_station_gas_service gss
                    INNER JOIN gas_service gs ON gss.gas_service_id = gs.id
                    AND gss.gas_station_id = s.id) as gas_services
  
                    FROM gas_station s 
                    INNER JOIN address a ON s.address_id = a.id
                    WHERE a.longitude IS NOT NULL AND a.latitude IS NOT NULL $gasTypeFilter $cityFilter $departmentFilter
                    HAVING `distance` < $radius
                    ORDER BY `distance` ASC LIMIT 250;
        ";

        $statement = $this->getEntityManager()->getConnection()->prepare($query);

        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.id IN (:ids)')
            ->setParameter('ids', $statement->executeQuery()->fetchFirstColumn())
            ->getQuery();

        return $query->getResult();
    }

    private function createGasTypeFilter(string $gasTypeUuid)
    {
        $query = " AND (JSON_KEYS(s.last_gas_prices) LIKE '%$gasTypeUuid%')";

        return $query;
    }

    private function createGasServicesFilter($filters)
    {
        $query = '';
        if (array_key_exists('gas_service', $filters ?? []) && '' !== $filters['gas_service']) {
            $gasServices = explode(',', $filters['gas_service']);
            $query = ' AND (';
            foreach ($gasServices as $gasService) {
                $query .= "`gas_services` LIKE '%".trim($gasService)."%' OR ";
            }
            $query = mb_substr($query, 0, -4);
            $query .= ')';
        }

        return $query;
    }

    private function createGasStationsCitiesFilter(?string $filterCity)
    {
        $query = '';
        if (null === $filterCity) {
            return $query;
        }
        $query = " AND a.postal_code IN ($filterCity)";

        return $query;
    }

    private function createGasStationsDepartmentsFilter(?string $filterDepartment)
    {
        $query = '';
        if (null === $filterDepartment) {
            return $query;
        }
        $query = " AND SUBSTRING(a.postal_code, 1, 2) IN ($filterDepartment)";

        return $query;
    }

    /**
     * @return GasStation[]
     *
     * @throws QueryException
     */
    public function getGasStationGooglePlaceByPlaceId(?GasStation $gasStation)
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.googlePlace', 'ss')
            ->where('ss.placeId = :placeId AND ss.placeId IS NOT NULL')
            ->andWhere('s.uuid != :uuid')
            ->setParameters([
                'placeId' => $gasStation?->getGooglePlace()->getPlaceId(),
                'uuid' => $gasStation?->getUuid(),
            ])
            ->getQuery();

        return $query->getResult();
    }
}
