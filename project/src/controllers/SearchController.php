<?php
namespace thewulf7\friendloc\controllers;


use thewulf7\friendloc\components\Controller;
use thewulf7\friendloc\models\User;

class SearchController extends Controller
{
    public function searchAction($q)
    {
        $service = $this->getElastic();
        $types   = [];

        foreach ($service->getEntities() as $entity)
        {
            $types[] = $entity->type;
        }

        $params = [
            'index' => 'users',
            'type'  => array_unique($types),
            'body'  => [
                'query' => [
                    "match" => [
                        '_all' => [
                            "query"     => $q,
                            "fuzziness" => "AUTO",
                        ],
                    ],
                ],
            ],
        ];

        $result  = $service->getClient()->search($params);
        $records = [];

        foreach ($result['hits']['hits'] as $record)
        {
            $records[$record['_id']] = $record;
        }

        $data = array_map(function ($result)
        {
            $model = $this->getUserService()->get($result['_id']);
            $loc   = $this->getLocationService()->getLocation($model->getId());

            return [
                'user'     => $model,
                'location' => $loc,
            ];
        }, $records);

        sort($data);

        $this->sendResponse(0, User::class, $data);
    }
}