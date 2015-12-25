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

        $name = explode(' ', $model->getName());
        $sign = count($name) > 1 ? $name[0][0] . $name[1][0] : $name[0][0] . $name[0][1];

        $locationService = $this->getLocationService();

        $this->render('default/index', [
            'user'    => [
                'name'     => $model->getName(),
                'sign'     => strtoupper($sign),
                'location' => $locationService->getLocation($model->getId())->getLocationName(),
            ],
            'friends' => array_map(function (User $user) use ($locationService)
            {
                return [
                    'id'       => $user->getId(),
                    'name'     => $user->getName(),
                    'sign'     => 'OK',
                    'location' => $locationService->getLocation($user->getId())->getLocationName(),
                    'link'     => '/user/' . $user->getId(),
                ];
            }, $this->getFriendsService()->getFriends($model->getId())),
        ]);
    }
}