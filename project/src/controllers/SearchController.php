<?php
namespace thewulf7\friendloc\controllers;


use thewulf7\friendloc\components\Controller;
use thewulf7\friendloc\models\User;

class SearchController extends Controller
{
    public function searchAction($q)
    {
        $cUser    = $this->getCurrentUser();
        $cFriends = array_map(function ($user)
        {
            return $user->getId();
        }, $this->getUserService()->getFriends($cUser->getId()));

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
                    'filtered' => [
                        'query' => [
                            'match'    => [
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
                $records['my'][$record['_id']] = [
                    'user'     => $this->getUserService()->get($record['_id']),
                    'location' => $this->getLocationService()->getLocation($record['_id']),
                ];

            } elseif (!array_key_exists($record['_id'], $records['other']))
            {
                $records['other'][$record['_id']] = [
                    'user'     => $this->getUserService()->get($record['_id']),
                    'location' => $this->getLocationService()->getLocation($record['_id']),
                ];
            }
        }


        sort($records['my']);
        sort($records['other']);


        $this->sendResponse(0, User::class, $records);
    }
}