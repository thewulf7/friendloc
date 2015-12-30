<?php
namespace thewulf7\friendloc\controllers;


use DI\NotFoundException;
use Ivory\GoogleMap\Helper\MapHelper;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Overlays\InfoWindow;
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
        $autocomplete = $this->getMapService()->getAutocomplete($model->getLocationName());
        $mapRender    = $this->getMapService()->renderMapWithMarker($model->getLatlng(), $model->getLocationName());


        return $this->sendResponse(
            [
                'id'         => $model->getId(),
                'type'       => User::class,
                'properties' => [
                    'user'     => $model,
                    'isFriend' => in_array($model->getId(), $cUser->getFriendList(), false),
                    'location' => [
                        'html'  => htmlentities(trim($mapRender['html'])),
                        'js'    => htmlentities(trim($mapRender['js'])),
                        'htmlA' => htmlentities(trim($autocomplete['html'])),
                        'jsA'   => htmlentities(trim($autocomplete['js'])),
                    ],
                ],
            ]
        );
    }

    /**
     * Update user
     *
     * @param $id
     */
    public function updateAction(int $id)
    {
        $params = $this->getRequest()->getBodyParams();

        $name         = $params['name'];
        $email        = $params['email'];
        $spassword    = $params['password'];
        $newPassword  = $params['newpassword'];
        $rnewPassword = $params['rnewpassword'];
        $locationName = $params['locationName'];
        $location     = [$params['lat'], $params['lng']];

        try
        {
            $model    = $this->getUserService()->get($id);
            $password = '';
            if ($newPassword === $rnewPassword && Auth::createPassword($spassword, $model->getSalt()) === $model->getPasswd())
            {
                $password = Auth::createPassword($newPassword, $model->getSalt());
            }
            $model = $this->getUserService()->update($model->getId(), $name, $email, $password, $locationName, $location);
        } catch (\InvalidArgumentException $e)
        {
            return $this->sendErrorResponse([$e->getMessage()]);
        }

        $this->sendResponse(['code' => 200]);
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

        $map = $this->getMapService()->renderMapWithMarker($friend->getLatlng(), $friend->getLocationName());

        $this->sendResponse(
            [
                'id'         => $friend->getId(),
                'type'       => User::class,
                'properties' => [
                    'user'     => $friend,
                    'isFriend' => true,
                    'location' => [
                        'html' => htmlentities($map['html']),
                        'js'   => htmlentities(trim($map['js'])),
                    ],
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

        $map = $this->getMapService()->renderMapWithMarker($friend->getLatlng(), $friend->getLocationName());

        $this->sendResponse(
            [
                'id'         => $friend->getId(),
                'type'       => User::class,
                'properties' => [
                    'user'     => $friend,
                    'isFriend' => false,
                    'location' => [
                        'html' => htmlentities($map['html']),
                        'js'   => htmlentities(trim($map['js'])),
                    ],
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

    public function getDirectionsAction(int $id)
    {
        $model = $this->getCurrentUser();
        try
        {
            $friend = $this->getUserService()->get($id);
        } catch (\Exception $e)
        {
            return $this->sendErrorResponse([$e->getMessage()]);
        }

        return $this->sendResponse(
            [
                'id'         => $id,
                'type'       => User::class,
                'properties' => [
                    'user'   => [
                        'lat' => $model->getLatlng()->getLatitude(),
                        'lng' => $model->getLatlng()->getLongitude(),
                    ],
                    'friend' => [
                        'lat' => $friend->getLatlng()->getLatitude(),
                        'lng' => $friend->getLatlng()->getLongitude(),
                    ],
                ],
            ]
        );
    }
}