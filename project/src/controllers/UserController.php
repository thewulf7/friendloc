<?php
namespace thewulf7\friendloc\controllers;


use thewulf7\friendloc\components\Controller;

/**
 * Class UserController - REST Controller
 *
 * @package thewulf7\friendloc\controllers
 */
class UserController extends Controller
{
    /**
     * @param $id
     */
    public function viewAction($id)
    {
        echo 'usre '.$id;
    }

    /**
     * @param $id
     */
    public function updateAction($id)
    {
        echo 'usreU '.$id;
    }
}