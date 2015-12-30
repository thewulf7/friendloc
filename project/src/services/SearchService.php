<?php
namespace thewulf7\friendloc\services;


use thewulf7\friendloc\components\AbstractService;

/**
 * Class SearchService
 *
 * @package thewulf7\friendloc\services
 */
class SearchService extends AbstractService
{
    /**
     * Search by query
     *
     * @param string $index
     * @param string $type
     * @param array  $query
     * @param array  $sort
     *
     * @return mixed
     */
    public function search(string $index, string $type, array $query, array $sort = []): array
    {
        $params = [
            'index' => $index,
            'type'  => $type,
            'body'  => [
                'query' => $query,
            ],
        ];

        if (count($sort) > 0)
        {
            $params['body']['sort'] = $sort;
        }

        $response = $this->getElastic()->getClient()->search($params);

        return array_map(function ($record)
        {
            return $this->getUserService()->get($record['_id']);
        }, $response['hits']['hits']);
    }
}