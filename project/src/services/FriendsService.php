<?php
namespace thewulf7\friendloc\services;


use thewulf7\friendloc\components\AbstractService;
use thewulf7\friendloc\components\ElasticSearch;

/**
 * Class FriendsService
 *
 * @package thewulf7\friendloc\services
 */
class FriendsService extends AbstractService
{
    /**
     * @param int $userId
     *
     * @return array
     */
    public function getFriends(int $userId): array
    {
        return [];
    }

    /**
     * @param int $userId
     * @param int $friendId
     *
     * @return mixed
     */
    public function addToFriends(int $userId, int $friendId)
    {
        return true;
    }

    /**
     * @param int $userId
     * @param int $friendId
     *
     * @return mixed
     */
    public function removeFromFriends(int $userId, int $friendId)
    {
        return true;
    }
}