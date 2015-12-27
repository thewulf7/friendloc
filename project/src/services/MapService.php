<?php
namespace thewulf7\friendloc\services;


use Ivory\GoogleMap\Map;
use thewulf7\friendloc\components\AbstractService;

/**
 * Class MapService
 *
 * @package thewulf7\friendloc\services
 */
class MapService extends AbstractService
{
    public function createMap(): Map
    {
        $map = new Map();

        $map->setLanguage('en');
        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');

        $map->setAsync(false);

        return $map;
    }
}