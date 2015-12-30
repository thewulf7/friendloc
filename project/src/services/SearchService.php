<?php
namespace thewulf7\friendloc\services;


use thewulf7\friendloc\components\AbstractService;

class SearchService extends AbstractService
{
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