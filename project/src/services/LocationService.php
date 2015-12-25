<?php
namespace thewulf7\friendloc\services;


use CrEOF\Spatial\PHP\Types\Geography\Point;
use thewulf7\friendloc\components\AbstractService;
use thewulf7\friendloc\models\Location;
use thewulf7\friendloc\components\ElasticSearch;

/**
 * Class LocationService
 *
 * @package thewulf7\friendloc\services
 */
class LocationService extends AbstractService
{
    /**
     * @param int     $userId
     * @param string  $name
     * @param array   $latlng
     */
    public function addLocation(int $userId, string $name, array $latlng)
    {
        /** @var ElasticSearch $service */
        $service = $this->getElastic();

        $model = new Location();
        $user = $this->getUserService()->get($userId);

        $model
            ->setLocationName($name)
            ->setId($user->getId())
            ->setUserName($user->getName())
            ->setLatlng($latlng);

        $service->persist($model);
    }

    /**
     * @param int|int $userId
     * @param string  $name
     * @param array   $latlng
     */
    public function changeLocation(int $userId, string $name, array $latlng)
    {
        /** @var ElasticSearch $service */
        $service = $this->getElastic();

        /** @var Location $entity */
        $entity = $service->find('Location', 'location_' . $userId);

        $entity
            ->setLocationName($name)
            ->setLatlng($latlng);

        $service->persist($entity);
    }

    /**
     * @param int $userId
     *
     * @return Location
     */
    public function getLocation(int $userId)
    {
        /** @var ElasticSearch $service */
        $service = $this->getElastic();

        /** @var Location $entity */
        $entity = $service->find('Location', $userId);

        return $entity;
    }
}