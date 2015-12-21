<?php
namespace thewulf7\friendloc\services;


use thewulf7\friendloc\components\AbstractService;
use thewulf7\friendloc\components\Auth;
use thewulf7\friendloc\models\User;

class UserService extends AbstractService
{
    public function create($email, $name, $passwd = ''): int
    {
        $entityManager = $this->getEntityManager();
        $salt          = Auth::generateSalt();
        $passwd        = $passwd ?? Auth::generatePassword();

        $model = new User();
        $model
            ->setEmail($email)
            ->setName($name)
            ->setPasswd(Auth::createPassword($passwd, $salt))
            ->setSalt($salt)
            ->setApproved(false)
            ->setCreated(new \DateTime("now"));

        $entityManager->persist($model);
        $entityManager->flush();

        return $model->getId();
    }

    public function get(int $id): User
    {
        $entityManager = $this->getEntityManager();

        $model = $entityManager->find('User', $id);

        return $model;
    }

    public function delete(int $id)
    {
        $entityManager = $this->getEntityManager();

        $model = $entityManager->find('User', $id);

        $entityManager->remove($model);

        try {
            $entityManager->flush();
        } catch(\Doctrine\ORM\OptimisticLockException $e) {
            return false;
        }

        return true;
    }

    public function update(int $id, string $name = '', string $password = ''): User
    {
        $entityManager = $this->getEntityManager();

        $model = $entityManager->find('User', $id);

        if (strlen($name) > 0)
        {
            $model->setName($name);
        }

        if (strlen($password) > 0)
        {
            $salt = Auth::generateSalt();
            $model->setPasswd(Auth::createPassword($password, $salt));
        }

        $entityManager->flush();

        return $model;
    }
}