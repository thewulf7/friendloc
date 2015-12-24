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

        $this->render('default/index', [
            'user'    => [
                'name'     => $model->getName(),
                'sign'     => strtoupper($sign),
                'location' => $this->getLocationService()->getLocation($model->getId())->getLocationName(),
            ],
            'friends' => [
                [
                    'name'     => 'Ostroumova Kate',
                    'sign'     => 'OK',
                    'location' => 'Russia, St.Petersburg, Kirillovskaya 18',
                    'link'     => '/user/1',
                ],
            ],
        ]);
    }
}