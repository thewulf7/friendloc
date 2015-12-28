<?php
namespace thewulf7\friendloc\controllers;


use Ivory\GoogleMap\Base\Coordinate;
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
    public function guestAllowedMethods(): array
    {
        $methods = parent::guestAllowedMethods();

        return array_merge($methods, [
            'loginAction',
            'signupAction',
        ]);
    }

    /**
     * Login page & action
     */
    public function loginAction()
    {
        $query = $this->getRequest()->getQuery();

        if($this->getRequest()->getMethod() === 'GET' && isset($query['newuser']) && $query['newuser'] === 'true')
        {
            return $this->render('/auth/login', [
                'notice' => [
                    'Thanks for registration.',
                    'Confirmation email with password has been send to your email.'
                ],
            ]);
        }

        if ($this->getRequest()->getMethod() === 'POST')
        {
            $params = $this->getRequest()->getBodyParams();
            /** @var AuthService $service */
            $service = $this->getAuthService();

            if ($service->auth($params['email'], $params['password']) instanceof User)
            {
                $this->redirect('/');
            } else
            {
                return $this->render('/auth/login', [
                    'errors' => ['Wrong email or password'],
                ]);
            }
        }

        $this->render('/auth/login');
    }

    /**
     * Render signup form
     */
    public function signupAction()
    {
        if ($this->getRequest()->getMethod() === 'POST')
        {
            return $this->redirect('/auth/login?newuser=true');
        }
        $autocomplete = $this->getMapService()->getAutocomplete();

        $map = $this->getMapService()->createEmptyMap(new Coordinate());
        $mapRender = $this->getMapService()->renderMap($map);

        return $this->render('/auth/signup', [
            'location' => [
                'map'  => trim($mapRender['html']),
                'html' => trim($autocomplete['html']),
                'js'   => trim($autocomplete['js']) . trim($mapRender['js']),
            ],
        ]);
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