<?php
namespace thewulf7\friendloc\models;

use thewulf7\friendloc\components\elasticsearch\annotations as ElasticSearch;
use thewulf7\friendloc\components\elasticsearch\Model;
use CrEOF\Spatial\PHP\Types\Geography\Point;

/**
 * @ElasticSearch\Entity(index="users", type="location", number_of_shards=1, number_of_replicas=1, autocomplete=true)
 */
class Location implements Model,\JsonSerializable
{
    /**
     * @ElasticSearch\Id
     * @ElasticSearch\ElasticField(type="string", includeInAll=false)
     * @var string
     */
    private $id;

    /**
     * @ElasticSearch\ElasticField(type="string", includeInAll=true)
     * @var string
     */
    private $locationName;

    /**
     * @ElasticSearch\ElasticField(type="geo_point", includeInAll=true)
     * @var Point
     */
    private $latlng;

    /**
     * Print entity as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'locationName' => $this->getLocationName(),
            'latlng'       => [
                $this->getLatlng()->getLatitude(),
                $this->getLatlng()->getLongitude(),
            ],
        ];
    }

    /**
     * Get Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Id
     *
     * @param string $id
     *
     * @return Location
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get Latlng
     *
     * @return Point
     */
    public function getLatlng(): Point
    {
        return $this->latlng;
    }

    /**
     * Set latlng
     *
     * @param Point $latlng
     *
     * @return Location
     */
    public function setLatlng(array $latlng): Location
    {
        $this->latlng = new Point($latlng);

        return $this;
    }

    /**
     * Get LocationName
     *
     * @return string
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     * Set locationName
     *
     * @param string $locationName
     *
     * @return Location
     */
    public function setLocationName($locationName)
    {
        $this->locationName = $locationName;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'locationName' => $this->getLocationName(),
            'latlng'       => $this->getLatlng(),
        ];
    }
}