<?php
namespace thewulf7\friendloc\controllers;


use thewulf7\friendloc\components\Controller;
use thewulf7\friendloc\models\User;

class UserController extends Controller
{
    public function view($id)
    {
        $model = $this->getEntityManager();
    }
}