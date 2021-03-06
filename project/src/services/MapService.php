<?php
namespace thewulf7\friendloc\services;


use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Helper\MapHelper;
use Ivory\GoogleMap\Helper\Places\AutocompleteHelper;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\Overlays\InfoWindow;
use Ivory\GoogleMap\Overlays\Marker;
use Ivory\GoogleMap\Places\Autocomplete;
use Ivory\GoogleMap\Places\AutocompleteType;
use Ivory\GoogleMap\Services\Directions\Directions;
use Ivory\GoogleMap\Services\Directions\DirectionsRequest;
use Ivory\GoogleMap\Services\Geocoding\Geocoder;
use Ivory\GoogleMap\Services\Geocoding\GeocoderProvider;
use thewulf7\friendloc\components\AbstractService;
use Widop\HttpAdapter\CurlHttpAdapter;

/**
 * Class MapService
 *
 * @package thewulf7\friendloc\services
 */
class MapService extends AbstractService
{
    /**
     * Create empty map
     *
     * @param Coordinate $center
     * @param bool       $async
     *
     * @return Map
     * @throws \Ivory\GoogleMap\Exception\AssetException
     * @throws \Ivory\GoogleMap\Exception\MapException
     */
    public function createEmptyMap(Coordinate $center, bool $async = true): Map
    {
        $map = new Map();
        $map->setLanguage('en');
        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');
        $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);

        $map->setAutoZoom(false);
        $map->setCenter($center);
        $map->setMapOption('zoom', 2);
        $map->setJavascriptVariable('location_map');

        $map->setAsync($async);
        $map->setStylesheetOptions(
            [
                'width'  => '100%',
                'height' => '300px',
            ]
        );

        return $map;
    }

    /**
     * Render map
     *
     * @param Map $map
     *
     * @return mixed
     */
    public function renderMap(Map $map): array
    {
        $mapHelper = new MapHelper();
        $jsMap     = $mapHelper->renderJsLibraries($map);
        $jsMap     = $this->removeJsCaller($jsMap);

        $jsMap .= '<script type="text/javascript"> function load_ivory_google_map(){' . $mapHelper->renderJsContainer($map) . '}</script>';

        return [
            'html' => $mapHelper->renderHtmlContainer($map) . $mapHelper->renderStylesheets($map),
            'js'   => $jsMap,
        ];
    }

    /**
     * Render map with marker
     *
     * @param Coordinate $point
     * @param string     $info
     * @param bool       $async
     *
     * @return mixed
     * @throws \Ivory\GoogleMap\Exception\AssetException
     * @throws \Ivory\GoogleMap\Exception\MapException
     * @throws \Ivory\GoogleMap\Exception\OverlayException
     */
    public function renderMapWithMarker(Coordinate $point, string $info = '', bool $async = true): array
    {
        $marker     = new Marker();
        $infoWindow = new InfoWindow();

        $marker->setPrefixJavascriptVariable('marker_');
        $marker->setPosition($point);
        $marker->setAnimation(Animation::DROP);

        $marker->setOption('clickable', true);
        $marker->setOption('flat', true);

        if ($info)
        {
            $infoWindow->setPrefixJavascriptVariable('info_window_');
            $infoWindow->setContent("<p>{$info}</p>");
            $infoWindow->setOpen(false);
            $infoWindow->setAutoOpen(true);
            $marker->setInfoWindow($infoWindow);
        }

        $map = $this->createEmptyMap($point, $async);

        $map->setMapOption('zoom', 13);
        $map->addMarker($marker);

        return $this->renderMap($map);
    }

    /**
     * Get autocomplete form
     *
     * @param null $value
     *
     * @return mixed
     * @throws \Ivory\GoogleMap\Exception\AssetException
     * @throws \Ivory\GoogleMap\Exception\PlaceException
     * @throws \Ivory\GoogleMap\Exception\TemplatingException
     */
    public function getAutocomplete($value=null): array
    {
        $autocomplete       = new Autocomplete();
        $autocompleteHelper = new AutocompleteHelper();

        $autocomplete->setPrefixJavascriptVariable('location_autocomplete_');
        $autocomplete->setInputId('location_input');

        $autocomplete->setInputAttributes(['class' => 'form-control', 'name' => 'locationName', 'required' => 'required']);

        $autocomplete->setInputAttribute('value', $value);

        $autocomplete->setJavascriptVariable('location_autocomplete');

        $autocomplete->setTypes([AutocompleteType::GEOCODE]);

        $autocomplete->setAsync(true);
        $autocomplete->setLanguage('en');

        $js = str_replace('load_ivory_google_map_api', 'load_ivory_google_map_api_auto', $autocompleteHelper->renderJavascripts($autocomplete));

        return [
            'html'   => $autocompleteHelper->renderHtmlContainer($autocomplete),
            'js'     => $this->removeJsCaller($js),
        ];
    }

    /**
     * Remove special info
     *
     * @param string $js
     *
     * @return string
     */
    private function removeJsCaller(string $js): string
    {
        return preg_replace('/(\<script\stype=\"text\/javascript\"\ssrc=\"[\S\s]+\".*\<\/script>)/', '', $js);
    }

    /**
     * Geocode info
     *
     * @param string $name
     *
     * @return mixed
     */
    public function geoCode(string $name): array
    {
        $arResult = [];
        $geocoder = new Geocoder();
        $geocoder->registerProviders(
            [
                new GeocoderProvider(new CurlHttpAdapter()),
            ]
        );

        $response = $geocoder->geocode($name);

        foreach ($response->getResults() as $result)
        {
            $arResult[] = $result->getGeometry()->getLocation();
        }

        return $arResult;
    }

    /**
     * Get directions info
     *
     * @param Coordinate $from
     * @param Coordinate $to
     *
     * @return mixed
     * @throws \Ivory\GoogleMap\Exception\DirectionsException
     */
    public function getDirections(Coordinate $from, Coordinate $to): array
    {
        $directions = new Directions(new CurlHttpAdapter());

        $directionsRequest = new DirectionsRequest();

        $directionsRequest->setOrigin($from);
        $directionsRequest->setDestination($to);

        $response = $directions->route($directionsRequest);

        return $response->getRoutes();
    }
}