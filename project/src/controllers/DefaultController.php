<?php
namespace thewulf7\friendloc\controllers;


use thewulf7\friendloc\components\Controller;

class DefaultController extends Controller
{
    public function indexAction($action)
    {
        $time = new \DateTime('+1 month');

        echo $time->format('Y-m-d');
    }
}