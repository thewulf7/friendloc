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
            'approveAction',
        ]);
    }

    /**
     * Login page & action
     */
    public function loginAction()
    {
        $query = $this->getRequest()->getQuery();

        if ($this->getRequest()->getMethod() === 'GET' && isset($query['newuser']) && $query['newuser'] === 'true')
        {
            return $this->render('/auth/login', [
                'notice' => [
                    'Thanks for registration.',
                    'Confirmation email with password has been send to your email.',
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
        $autocomplete = $this->getMapService()->getAutocomplete();

        if ($this->getRequest()->getMethod() === 'POST')
        {
            $params = $this->getRequest()->getBodyParams();

            $name         = $params['name'];
            $email        = $params['email'];
            $locationName = $params['locationName'];
            $lat          = $params['location']['lat'];
            $lng          = $params['location']['lng'];

            try
            {
                $salt   = Auth::generateSalt();
                $passwd = Auth::generatePassword();

                $user = $this->getUserService()->create($email, $name, $passwd, $salt, $locationName, [$lat, $lng]);

                $this->getEmailService()->sendConfirmationEmail($user, $passwd);

                return $this->redirect('/auth/login?newuser=true');
            } catch (\Exception $e)
            {

                $map       = $this->getMapService()->createEmptyMap(new Coordinate($lat, $lng));
                $mapRender = $this->getMapService()->renderMap($map);

                return $this->render('/auth/signup', [
                    'errors'   => [
                        $e->getMessage(),
                    ],
                    'data'     => [
                        'name'         => $name,
                        'email'        => $email,
                        'locationName' => $locationName,
                        'lat'          => $lat,
                        'lng'          => $lng,
                    ],
                    'location' => [
                        'map'  => trim($mapRender['html']),
                        'html' => trim($autocomplete['html']),
                        'js'   => trim($autocomplete['js']) . trim($mapRender['js']),
                    ],
                ]);
            }
        }

        $map       = $this->getMapService()->createEmptyMap(new Coordinate());
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

    /**
     * Approve user
     *
     * @param $hash
     */
    public function approveAction($hash)
    {
        try
        {
            $model = $this->getAuthService()->authByHash($hash);
            if ($model)
            {
                $hash = hash('sha1', time() . '|' . $model->getId());

                $model->setUserhash($hash);
                $model->setApproved(true);
                $this->getEntityManager()->persist($model);
                $this->getEntityManager()->flush();

                Auth::setAuth($hash);

                $this->getEmailService()->sendSuccessEmail($model);

                $this->redirect('/');
            } else
            {
                echo 'Wrong hash given.';
            }
        } catch (\Exception $e)
        {
            echo 'Wrong hash given.';
        }
    }
}