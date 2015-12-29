<?php
namespace thewulf7\friendloc\controllers;


use thewulf7\friendloc\components\Controller;
use thewulf7\friendloc\models\User;

class SearchController extends Controller
{
    public function searchAction($q)
    {
        $cUser       = $this->getCurrentUser();
        $userService = $this->getUserService();
        $service     = $this->getElastic();
        $types       = [];

        $cFriends = array_map(function ($userId) use ($userService)
        {
            return $userService->get($userId)->getId();
        }, $cUser->getFriendList());

        foreach ($service->getEntities() as $entity)
        {
            $types[] = $entity->type;
        }

        $params = [
            'index' => 'users',
            'type'  => array_unique($types),
            'body'  => [
                'sort'  => [
                    [
                        '_geo_distance' => [
                            'latlng' => [
                                'lat' => (double)$cUser->getLatlng()->getLatitude(),
                                'lon' => (double)$cUser->getLatlng()->getLongitude()
                            ],
                            'order'  => 'asc',
                            'unit'   => 'miles',
                        ],
                    ],
                ],
                'query' => [
                    'filtered' => [
                        'query'  => [
                            'match' => [
                                '_all' => [
                                    'query'     => $q,
                                    'fuzziness' => 'AUTO',
                                ],
                            ],
                        ],
                        'filter' => [
                            'bool' => [
                                'must_not' => [
                                    'ids' => [
                                        'values' => [$cUser->getId()],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result  = $service->getClient()->search($params);
        $records = [
            'my'    => [],
            'other' => [],
        ];

        foreach ($result['hits']['hits'] as $record)
        {
            if (!array_key_exists($record['_id'], $records['my']) && in_array($record['_id'], $cFriends, false))
            {
                $records['my'][$record['_id']] = $this->getUserService()->get($record['_id']);

            } elseif (!array_key_exists($record['_id'], $records['other']))
            {
                $records['other'][$record['_id']] = $this->getUserService()->get($record['_id']);
            }
        }


        sort($records['my']);
        sort($records['other']);


        return $this->sendResponse(
            [
                'properties' => ['result' => $records],
            ]
        );
    }
}