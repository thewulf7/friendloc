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

        $this->sendResponse($model->getId(), User::class,
                            [
                                'name'  => $model->getName(),
                                'email' => $model->getEmail(),
                                'date'  => $model->getCreated()->format('Y-m-d H:i:s'),
                            ]
        );
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

        $friendId = $this->getFriendsService()->addToFriends($model->getId(), $this->getRequest()->getBodyParams('id'));

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

        $friends = $this->getFriendsService()->getFriends($id);

        $this->sendResponse($id, User::class,
                            [
                                'friends' => $friends,
                            ]
        );
    }

}