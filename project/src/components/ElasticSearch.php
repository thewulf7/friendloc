<?php
namespace thewulf7\friendloc\components;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use thewulf7\friendloc\components\elasticsearch\Model;


/**
 * Class ElasticSearch
 *
 * @package thewulf7\friendloc\components
 */
class ElasticSearch
{
    /**
     * @var \Elasticsearch\Client
     */
    private $_client;

    /**
     * @var array
     */
    private $_entities = [];

    /**
     * @var array
     */
    private $_mappings = [];

    /**
     * @var array
     */
    private $_classes = [];

    /**
     * ElasticSearch constructor.
     *
     * @param \Elasticsearch\Client $client
     * @param array                 $entityPaths
     */
    public function __construct(\Elasticsearch\Client $client, array $entityPaths)
    {
        $this->_client = $client;

        AnnotationRegistry::registerFile(__DIR__ . '/elasticsearch/annotations/Entity.php');
        AnnotationRegistry::registerFile(__DIR__ . '/elasticsearch/annotations/ElasticField.php');
        AnnotationRegistry::registerFile(__DIR__ . '/elasticsearch/annotations/Id.php');

        $reader   = new AnnotationReader();
        $mappings = [];

        foreach ($entityPaths as $entityPath)
        {
            $dir = new \DirectoryIterator($entityPath);

            foreach ($dir as $fileinfo)
            {
                if ($fileinfo->getExtension() === 'php')
                {
                    $className = str_replace('.php', '', $fileinfo->getFilename());
                    $content   = file_get_contents($fileinfo->getPath() . '/' . $fileinfo->getFilename());
                    preg_match('/namespace\s([\S]*)\;/', $content, $namespace);

                    $class = new \ReflectionClass($namespace[1] . '\\' . $className);

                    $entity = $reader->getClassAnnotation($class, 'thewulf7\friendloc\components\elasticsearch\annotations\Entity');

                    if ($entity === null)
                    {
                        continue;
                    }

                    $properties = [];

                    $props = $class->getProperties();

                    foreach ($props as $prop)
                    {
                        $idprop = $reader->getPropertyAnnotation($prop, 'Doctrine\ORM\Mapping\Id');

                        if ($idprop === null)
                        {

                            $annotation = $reader->getPropertyAnnotation($prop, 'thewulf7\friendloc\components\elasticsearch\annotations\ElasticField');

                            if ($annotation)
                            {
                                $proper = [
                                    'type'           => $annotation->type,
                                    'include_in_all' => $annotation->includeInAll,
                                ];

                                if ($annotation->type === 'geo_point')
                                {
                                    $proper['lat_lon'] = true;
                                }

                                $properties[$prop->getName()] = $proper;
                            }
                        }
                    }

                    $mappings[$entity->index]['mappings'][$entity->type] = [
                        '_all'       => [
                            'index_analyzer' => 'autocomplete',
//                            'search_analyzer' => 'autocomplete',
                        ],
                        'properties' => $properties,
                    ];
                    $mappings[$entity->index]['settings']                = [
                        'number_of_shards'   => $entity->number_of_shards,
                        'number_of_replicas' => $entity->number_of_replicas,
                        'autocomplete'       => $entity->autocomplete,
                    ];

                    $this->_entities[$className] = $entity;
                    $this->_classes[$className]  = $class;
                }
            }
        }

        foreach ($mappings as $index => $mapp)
        {
            $this->_mappings[] = [
                'index' => $index,
                'body'  => [
                    'mappings' => $mapp['mappings'],
                    'settings' => $mapp['settings'],
                ],
            ];
        }
    }

    /**
     * Get Client
     *
     * @return \Elasticsearch\Client
     */
    public function getClient(): \Elasticsearch\Client
    {
        return $this->_client;
    }

    /**
     * Get Entities
     *
     * @return array
     */
    public function getEntities()
    {
        return $this->_entities;
    }

    /**
     * Get Mappings
     *
     * @return array
     */
    public function getMappings()
    {
        return $this->_mappings;
    }

    /**
     * Get Classes
     *
     * @return array
     */
    public function getClasses()
    {
        return $this->_classes;
    }

    /**
     * @param array $entity
     *
     * @return bool
     */
    public function createIndex(array $entity)
    {
        try
        {
            $this->getClient()->indices()->create($entity);
        } catch (\Elasticsearch\Common\Exceptions\BadRequest400Exception $e)
        {
            echo $e->getMessage() . "\n";

            return false;
        }

        return true;
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    public function deleteIndex($entity)
    {
        try
        {
            $this->getClient()->indices()->delete($entity);
        } catch (\Elasticsearch\Common\Exceptions\BadRequest400Exception $e)
        {
            return false;
        }

        return true;
    }

    /**
     * Save entity
     *
     * @param \JsonSerializable $entity
     */
    public function persist(\JsonSerializable $entity)
    {
        $class = new \ReflectionClass($entity);

        $entityModel = $this->getEntities()[$class->getShortName()];

        $params = [
            'type'  => $entityModel->type,
            'index' => $entityModel->index,
            'id'    => (string)$entity->getId(),
        ];

        try
        {
            $this->getClient()->get($params);

            $params['body']['doc'] = $entity->jsonSerialize();

            $this->getClient()->update($params);

        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e)
        {
            $params['body'] = $entity->jsonSerialize();
            $this->getClient()->index($params);
        }
    }

    /**
     * @param \JsonSerializable $entity
     *
     * @return bool
     */
    public function remove(\JsonSerializable $entity)
    {
        $class = new \ReflectionClass($entity);

        if (!$entity->getId())
        {
            return false;
        }

        $entityModel = $this->getEntities()[$class->getShortName()];

        $params = [
            'type'  => $entityModel->type,
            'index' => $entityModel->index,
            'id'    => $entity->getId(),
        ];

        $this->getClient()->delete($params);
    }

    /**
     * Find entity by id
     *
     * @param string $entityName
     * @param        $id
     *
     * @return bool
     */
    public function find(string $entityName, $id)
    {
        $entityModel = $this->getEntities()[$entityName];

        $params = [
            'type'  => $entityModel->type,
            'index' => $entityModel->index,
            'id'    => $id,
        ];
        try
        {
            $item = $this->getClient()->get($params);
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e)
        {
            return false;
        }

        $model = $this->getClasses()[$entityName]->newInstance();
        $model->setId($item['_id']);

        foreach ($item['_source'] as $paramName => $paramValue)
        {
            $method = 'set' . ucfirst($paramName);
            if (method_exists($model, $method))
            {
                $model->$method($paramValue);
            }
        }

        return $model;
    }

    /**
     * Find entity by params
     *
     * @param string $entityName
     * @param array  $params
     *
     * @return array
     */
    public function findBy(string $entityName, array $params = [])
    {
        $arResult = [];

        $entityModel = $this->getEntities()[$entityName];

        $params = [
            'type'  => $entityModel->type,
            'index' => $entityModel->index,
            'body'  => [
                'query' => [
                    'match' => $params,
                ],
            ],
        ];

        $hits = $this->getClient()->search($params);

        foreach ($hits['hits']['hits'] as $item)
        {
            $model = $this->getClasses()[$entityName]->newInstance();
            $model->setId($item['_id']);

            foreach ($item['_source'] as $paramName => $paramValue)
            {
                $method = 'set' . ucfirst($paramName);
                $model->$method($paramValue);
            }

            $arResult[] = $model;
        }

        return $arResult;
    }
}