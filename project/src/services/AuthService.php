<?php
namespace thewulf7\friendloc\services;


use thewulf7\friendloc\components\AbstractService;
use thewulf7\friendloc\components\Auth;
use thewulf7\friendloc\models\User;

/**
 * Class AuthService
 *
 * @package thewulf7\friendloc\services
 */
class AuthService extends AbstractService
{
    /**
     * @param string $email
     * @param string $password
     *
     * @return bool|User
     */
    public function auth(string $email, string $password)
    {
        $entityManager = $this->getEntityManager();

        /** @var User $model */
        $model = $entityManager->getRepository('thewulf7\friendloc\models\User')->findOneBy(['email' => $email]);

        if(!$model)
        {
            return false;
        }

        $hashpasswd = crypt($password, $model->getSalt());

        if($hashpasswd === $model->getPasswd())
        {

            $hash = hash('sha1', time() . '|' . $model->getId());
            $model->setUserhash($hash);

            $entityManager->flush();

            Auth::setAuth($hash);

            return $model;
        } else {
            return false;
        }
    }

    /**
     * @param string $hash
     *
     * @return bool|User
     */
    public function authByHash(string $hash)
    {
        $entityManager = $this->getEntityManager();

        /** @var User $model */
        $model = $entityManager->getRepository('thewulf7\friendloc\models\User')->findOneBy(['userhash' => $hash]);

        $model = $model ? $this->getUserService()->get($model->getId()) : null;

        return $model ?? false;
    }

    /**
     * @param $hash
     */
    public function logout($hash)
    {
        /** @var User $model */
        $model = $this->authByHash($hash);
        $model->setUserhash(null);

        $this->getEntityManager()->flush();

        Auth::logout();
    }
}