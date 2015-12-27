<?php
namespace thewulf7\friendloc\controllers;


use thewulf7\friendloc\components\Controller;
use thewulf7\friendloc\models\User;

/**
 * Class DefaultController
 *
 * @package thewulf7\friendloc\controllers
 */
class DefaultController extends Controller
{
    /**
     * Index action
     */
    public function indexAction()
    {
        /** @var User $model */
        $model = $this->getCurrentUser();

        $userService = $this->getUserService();

        $this->render('default/index', [
            'user'    => $model->jsonSerialize(),
            'friends' => array_map(function ($userId) use ($userService)
            {
                return $userService->get($userId)->jsonSerialize();
            }, $model->getFriendList()),
        ]);
    }
}