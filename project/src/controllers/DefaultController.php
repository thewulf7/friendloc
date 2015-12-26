<?php
namespace thewulf7\friendloc\controllers;


use thewulf7\friendloc\components\Controller;
use thewulf7\friendloc\models\User;

class DefaultController extends Controller
{
    public function indexAction()
    {

        /** @var User $model */
        $model = $this->getCurrentUser();

        $locationService = $this->getLocationService();

        $this->render('default/index', [
            'user'    => [
                'id'       => $model->getId(),
                'name'     => $model->getName(),
                'sign'     => $model->getSign(),
                'location' => $locationService->getLocation($model->getId())->getLocationName(),
            ],
            'friends' => array_map(function (User $user) use ($locationService)
            {
                return [
                    'id'       => $user->getId(),
                    'name'     => $user->getName(),
                    'sign'     => $user->getSign(),
                    'location' => $locationService->getLocation($user->getId())->getLocationName(),
                    'link'     => '/users/' . $user->getId(),
                ];
            }, $this->getUserService()->getFriends($model->getId())),
        ]);
    }
}