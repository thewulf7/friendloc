<?php
namespace thewulf7\friendloc\services;


use thewulf7\friendloc\components\AbstractService;
use thewulf7\friendloc\components\Auth;
use thewulf7\friendloc\models\User;

class AuthService extends AbstractService
{
    public function auth(string $email,string $password)
    {
        $entityManager = $this->getEntityManager();

        /** @var User $model */
        $model = $entityManager->getRepository('User')->findOneBy(['email' => $email]);

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

    public function authByHash(string $hash)
    {
        $entityManager = $this->getEntityManager();

        /** @var User $model */
        $model = $entityManager->getRepository('User')->findOneBy(['userhash' => $hash]);

        return $model ?? false;
    }

    public function logout($hash)
    {
        /** @var User $model */
        $model = $this->authByHash($hash);
        $model->setUserhash(null);

        $this->getEntityManager()->flush();

        Auth::logout();
    }
}