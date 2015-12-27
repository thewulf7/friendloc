<?php
namespace thewulf7\friendloc\controllers;


use Ivory\GoogleMap\Helper\MapHelper;
use thewulf7\friendloc\components\Controller;

class MapController extends Controller
{
    public function getMapAction(int $userId)
    {
        $model = $this->getUserService()->get($userId);


        $map       = $this->getMapService()->createMap();
        $mapHelper = new MapHelper();

        $this->sendResponse(0, 'Map', [
            'html' => $mapHelper->renderHtmlContainer($map),
            'css'  => $mapHelper->renderStylesheets($map),
            'js'   => $mapHelper->renderJavascripts($map),
        ]);
    }
}