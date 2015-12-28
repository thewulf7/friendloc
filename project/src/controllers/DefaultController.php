<?php
namespace thewulf7\friendloc\controllers;


use Ivory\GoogleMap\Base\Coordinate;
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

        $autocomplete = $this->getMapService()->getAutocomplete();
        $map          = $this->getMapService()->createEmptyMap(new Coordinate($model->getLatlng()->getLatitude(), $model->getLatlng()->getLongitude()));
        $mapRender    = $this->getMapService()->renderMap($map);

        $this->render('default/index', [
            'user'     => $model->jsonSerialize(),
            'location' => [
                'map'  => trim($mapRender['html']),
                'html' => trim($autocomplete['html']),
                'js'   => trim($autocomplete['js']) . trim($mapRender['js']),
            ],
            'friends'  => array_map(function ($userId) use ($userService)
            {
                return $userService->get($userId)->jsonSerialize();
            }, $model->getFriendList()),
        ]);
    }
}