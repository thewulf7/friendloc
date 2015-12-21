<?php
namespace thewulf7\friendloc\components;


abstract class AbstractService
{
    use ApplicationHelper;

    public function init()
    {
        $services = require('ServiceLocator.php');

        foreach($services as $serviceName => $service)
        {
            $this->addToContainer($serviceName, $service);
        }
    }
}