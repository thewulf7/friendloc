<?php
namespace thewulf7\friendloc\controllers;


use thewulf7\friendloc\components\Controller;
use thewulf7\friendloc\models\User;

/**
 * Class SearchController
 *
 * @package thewulf7\friendloc\controllers
 */
class SearchController extends Controller
{
    /**
     * @param $q
     *
     * @return bool|\thewulf7\friendloc\components\bool
     */
    public function searchAction($q)
    {
        $cUser = $this->getCurrentUser();

        $cFriends = $cUser->getFriendList();

        $query = [
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
        ];

        $sort = [
            [
                '_geo_distance' => [
                    'latlng' => [
                        'lat' => (double)$cUser->getLatlng()->getLatitude(),
                        'lon' => (double)$cUser->getLatlng()->getLongitude(),
                    ],
                    'order'  => 'asc',
                    'unit'   => 'miles',
                ],
            ],
        ];

        $result  = $this->getSearchService()->search('users', 'user', $query, $sort);
        $records = [
            'my'    => [],
            'other' => [],
        ];

        foreach ($result as $user)
        {
            $id = $user->getId();

            if (!array_key_exists($id, $records['my']) && in_array($id, $cFriends, false))
            {
                $records['my'][$id] = $user;

            } elseif (!array_key_exists($id, $records['other']))
            {
                $records['other'][$id] = $user;
            }
        }

        return $this->sendResponse(
            [
                'properties' => ['result' => array_map(function ($group)
                {
                    return array_values($group);
                }, $records)],
            ]
        );
    }

    /**
     * @param int $distance
     *
     * @return bool|\thewulf7\friendloc\components\bool
     */
    public function nearestAction(int $distance = 100)
    {
        $cUser = $this->getCurrentUser();

        $query = [
            'filtered' => [
                'query'  => [
                    'match_all' => [],
                ],
                'filter' => [
                    'and' => [
                        [
                            'geo_distance' => [
                                'distance' => $distance . 'miles',
                                'latlng'   => [
                                    'lat' => (double)$cUser->getLatlng()->getLatitude(),
                                    'lon' => (double)$cUser->getLatlng()->getLongitude(),
                                ],
                            ],
                        ],
                        [
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

        $sort = [
            [
                '_geo_distance' => [
                    'latlng' => [
                        'lat' => (double)$cUser->getLatlng()->getLatitude(),
                        'lon' => (double)$cUser->getLatlng()->getLongitude(),
                    ],
                    'order'  => 'asc',
                    'unit'   => 'miles',
                ],
            ],
        ];

        $result = $this->getSearchService()->search('users', 'user', $query, $sort);

        return $this->sendResponse(
            [
                'properties' => ['result' => $result],
            ]
        );
    }
}