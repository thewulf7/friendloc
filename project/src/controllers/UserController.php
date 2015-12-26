<?php
namespace thewulf7\friendloc\controllers;


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
        $model = $this->getUserService()->get($id);
        $loc   = $this->getLocationService()->getLocation($model->getId());

        $this->sendResponse($model->getId(), User::class, [
            'user'     => $model,
            'location' => $loc,
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

        $model = $this->getUserService()->update($model->getId(), $name, $password);

        $this->sendResponse($model->getId(), User::class, [ 'status' => $model ? 'ok' : 'error']);
    }

    /**
     *  Add to friends action
     */
    public function addToFriendsAction()
    {
        $model = $this->getCurrentUser();

        $friendId = $this->getUserService()->addToFriends($model->getId(), $this->getRequest()->getBodyParams('id'));

        if ($friendId > 0)
        {
            $data = [
                'friendId' => $friendId,
            ];
        } else
        {
            $data = ['errors' => $friendId];
        }

        $this->sendResponse($model->getId(), User::class, $data);
    }

    /**
     * Get friends list
     *
     * @param int $id
     */
    public function getFriendsListAction(int $id)
    {
        $friends = $this->getUserService()->getFriends($id);

        $data = array_map(function ($result)
        {
            $model = $this->getUserService()->get($result['_id']);
            $loc   = $this->getLocationService()->getLocation($model->getId());

            return [
                'user'     => $model,
                'location' => $loc,
            ];
        }, $friends);

        $this->sendResponse($id, User::class, $data);
    }

}