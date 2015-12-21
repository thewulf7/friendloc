<?php
namespace thewulf7\friendloc\controllers;


use thewulf7\friendloc\components\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('/default/index');
    }
}