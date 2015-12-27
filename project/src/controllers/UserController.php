<?php
namespace thewulf7\friendloc\controllers;


use thewulf7\friendloc\components\Auth;
use thewulf7\friendloc\components\Controller;
use thewulf7\friendloc\models\User;

/**
 * Class UserController - REST Controller
 *
 * @package thewulf7\friendloc\controllers
 */
class UserController extends Controller
{

    /**
     * @param $id
     */
    public function viewAction(int $id)
    {
        $cUser   = $this->getCurrentUser();
        $friends = array_map(function ($user)
        {
            return $user->getId();
        }, $this->getUserService()->getFriends($cUser->getId()));

        $model = $this->getUserService()->get($id);
        $loc   = $this->getLocationService()->getLocation($model->getId());

        $this->sendResponse($model->getId(), User::class, [
            'user'     => $model,
            'location' => $loc,
            'isFriend' => in_array($model->getId(), $friends),
        ]);
    }

    /**
     * @param $id
     */
    public function updateAction(int $id)
    {
        $model = $this->getUserService()->get($id);

        $name     = $this->getRequest()->getBodyParams('name');
        $email    = $this->getRequest()->getBodyParams('email');
        $password = $this->getRequest()->getBodyParams('password');

        try
        {
            $model = $this->getUserService()->update($model->getId(), $name, $email, $password);
        } catch (\InvalidArgumentException $e)
        {
            return $this->sendResponse($model->getId(), User::class, ['errors' => [$e->getMessage()]]);
        }

        $this->sendResponse($model->getId(), User::class, ['status' => $model ? 'ok' : 'error']);
    }

    /**
     *  Add to friends action
     */
    public function addToFriendsAction()
    {
        $model = $this->getCurrentUser();

        try
        {
            $friend = $this->getUserService()->addToFriends($model->getId(), $this->getRequest()->getBodyParams('id'));
        } catch (\InvalidArgumentException $e)
        {
            return $this->sendResponse($model->getId(), User::class, ['errors' => [$e->getMessage()]]);
        }

        $loc = $this->getLocationService()->getLocation($friend->getId());

        $friends = array_map(function ($user)
        {
            return $user->getId();
        }, $this->getUserService()->getFriends($model->getId()));

        $this->sendResponse($friend->getId(), User::class, [
            'user'     => $friend,
            'location' => $loc,
            'isFriend' => in_array($friend->getId(), $friends),
        ]);
    }

    /**
     *  Remove from friends action
     */
    public function removeFromFriendsAction()
    {
        $model = $this->getCurrentUser();

        try
        {
            $friend = $this->getUserService()->removeFromFriends($model->getId(), $this->getRequest()->getBodyParams('id'));
        } catch (\InvalidArgumentException $e)
        {
            return $this->sendResponse($model->getId(), User::class, ['errors' => [$e->getMessage()]]);
        }

        $loc = $this->getLocationService()->getLocation($friend->getId());

        $friends = array_map(function ($user)
        {
            return $user->getId();
        }, $this->getUserService()->getFriends($model->getId()));

        $this->sendResponse($friend->getId(), User::class, [
            'user'     => $friend,
            'location' => $loc,
            'isFriend' => in_array($friend->getId(), $friends),
        ]);
    }

    /**
     * Get friends list
     *
     * @param int $id
     */
    public function getFriendsListAction(int $id)
    {
        $friends = $this->getUserService()->getFriends($id);

        $data = array_map(function ($model)
        {
            $loc   = $this->getLocationService()->getLocation($model->getId());

            return [
                'user'     => $model,
                'location' => $loc,
            ];
        }, $friends);

        $this->sendResponse($id, User::class, $data);
    }

}