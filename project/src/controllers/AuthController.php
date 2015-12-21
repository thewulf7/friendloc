<?php
namespace thewulf7\friendloc\controllers;


use thewulf7\friendloc\components\Auth;
use thewulf7\friendloc\components\Controller;
use thewulf7\friendloc\models\User;
use thewulf7\friendloc\services\AuthService;

/**
 * Class AuthController
 *
 * @package thewulf7\friendloc\controllers
 */
class AuthController extends Controller
{
    /**
     * Login page & action
     */
    public function loginAction()
    {
        if ($this->getRequest()->getMethod() === 'POST')
        {
            $params = $this->getRequest()->getBodyParams();
            /** @var AuthService $service */
            $service = $this->getAuthService();
            if($service->auth($params['email'], $params['passwd']) instanceof User)
            {
                $this->redirect('/profile');
            } else {
                return $this->render('/auth/login',[
                    'errors' => ['Wrong email or password']
                ]);
            }
        }

        return $this->render('/auth/login');
    }

    /**
     * Render signup form
     */
    public function signupAction()
    {
        return $this->render('/auth/signup');
    }

    /**
     * Logout action
     */
    public function logoutAction()
    {
        Auth::logout();
        $this->redirect('/');
    }
}