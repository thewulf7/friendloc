<?php
namespace thewulf7\friendloc\controllers;


use DI\NotFoundException;
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
     * GET view user
     *
     * @param int $id
     *
     * @return bool
     */
    public function viewAction(int $id)
    {
        $cUser = $this->getCurrentUser();

        try
        {
            $model = $this->getUserService()->get($id);
        } catch (NotFoundException $e)
        {
            return $this->sendErrorResponse([$e->getMessage()]);
        }

        return $this->sendResponse(
            [
                'id'         => $model->getId(),
                'type'       => User::class,
                'properties' => [
                    'user'     => $model,
                    'isFriend' => in_array($model->getId(), $cUser->getFriendList(), false),
                ],
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
        $model    = $this->getCurrentUser();
        $friendId = $this->getRequest()->getBodyParams('id');

        try
        {
            $friend = $this->getUserService()->addToFriends($model->getId(), $friendId);
        } catch (\InvalidArgumentException $e)
        {
            return $this->sendErrorResponse([$e->getMessage()]);
        } catch (NotFoundException $e)
        {
            return $this->sendErrorResponse([$e->getMessage()]);
        }

        $this->sendResponse(
            [
                'id'         => $model->getId(),
                'type'       => User::class,
                'properties' => [
                    'user'     => $friend,
                    'isFriend' => in_array($friend->getId(), $model->getFriendList(), false),
                ],
            ]
        );
    }

    /**
     *  Remove from friends action
     */
    public function removeFromFriendsAction()
    {
        $model    = $this->getCurrentUser();
        $friendId = $this->getRequest()->getBodyParams('id');

        try
        {
            $friend = $this->getUserService()->removeFromFriends($model->getId(), $friendId);
        } catch (\InvalidArgumentException $e)
        {
            return $this->sendErrorResponse([$e->getMessage()]);
        } catch (NotFoundException $e)
        {
            return $this->sendErrorResponse([$e->getMessage()]);
        }

        $this->sendResponse(
            [
                'id'         => $model->getId(),
                'type'       => User::class,
                'properties' => [
                    'user'     => $friend,
                    'isFriend' => in_array($friend->getId(), $model->getFriendList(), false),
                ],
            ]
        );
    }

    /**
     * Get friends list
     *
     * @param int $id
     *
     * @return bool
     */
    public function getFriendsListAction(int $id)
    {
        try
        {
            $friends = $this->getUserService()->getFriends($id);
        } catch (NotFoundException $e)
        {
            return $this->sendErrorResponse([$e->getMessage()]);
        }

        return $this->sendResponse(
            [
                'id'         => $id,
                'type'       => User::class,
                'properties' => [
                    'friends' => $friends,
                ],
            ]
        );
    }

}