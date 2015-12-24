<?php
namespace thewulf7\friendloc\services;


use thewulf7\friendloc\components\AbstractService;
use thewulf7\friendloc\components\Auth;
use thewulf7\friendloc\models\User;

/**
 * Class UserService
 *
 * @package thewulf7\friendloc\services
 */
class UserService extends AbstractService
{
    /**
     * @param        $email
     * @param        $name
     * @param string $passwd
     *
     * @return int
     */
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

    /**
     * @param int $id
     *
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function get(int $id): User
    {
        $entityManager = $this->getEntityManager();

        $model = $entityManager->find('thewulf7\friendloc\models\User', $id);

        return $model;
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function delete(int $id)
    {
        $entityManager = $this->getEntityManager();

        $model = $entityManager->find('thewulf7\friendloc\models\User', $id);

        $entityManager->remove($model);

        try {
            $entityManager->flush();
        } catch(\Doctrine\ORM\OptimisticLockException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param int    $id
     * @param string $name
     * @param string $password
     *
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function update(int $id, string $name = '', string $password = ''): User
    {
        $entityManager = $this->getEntityManager();

        $model = $entityManager->find('thewulf7\friendloc\models\User', $id);

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