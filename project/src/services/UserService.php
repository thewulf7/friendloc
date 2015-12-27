<?php
namespace thewulf7\friendloc\services;


use thewulf7\friendloc\components\AbstractService;
use thewulf7\friendloc\components\Auth;
use thewulf7\friendloc\components\ElasticSearch;
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

        try
        {
            $entityManager->flush();
        } catch (\Doctrine\ORM\OptimisticLockException $e)
        {
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
    public function update(int $id, string $name = '', string $email, string $password = ''): User
    {
        $entityManager = $this->getEntityManager();

        $model = $entityManager->find('thewulf7\friendloc\models\User', $id);

        if (strlen($name) > 0)
        {
            $model->setName($name);
        }

        if (strlen($email) > 0)
        {
            $model->setEmail($email);
        }

        if (strlen($password) > 0)
        {
            $salt = Auth::generateSalt();
            $model->setPasswd(Auth::createPassword($password, $salt));
        }

        $entityManager->flush();

        return $model;
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    public function getFriends(int $userId): array
    {
        /** @var User $entity */
        $entity = $this->getElastic()->find('User', $userId);

        return array_map(function ($friendId)
        {
            return $this->get($friendId);
        }, $entity->getFriendList());
    }

    /**
     * @param int $userId
     * @param int $friendId
     *
     * @return mixed
     */
    public function addToFriends(int $userId, int $friendId)
    {
        /** @var User $entity */
        $entity = $this->getElastic()->find('User', $userId);
        /** @var User $friend */
        $friend = $this->getElastic()->find('User', $friendId);

        $entity->addToFriendList($friend->getId());
        $friend->addToFriendList($entity->getId());

        $this->getElastic()->persist($entity);
        $this->getElastic()->persist($friend);

        return $this->getUserService()->get($friendId);

    }

    /**
     * @param int $userId
     * @param int $friendId
     *
     * @return mixed
     */
    public function removeFromFriends(int $userId, int $friendId)
    {
        /** @var User $entity */
        $entity = $this->getElastic()->find('User', $userId);
        /** @var User $friend */
        $friend = $this->getElastic()->find('User', $friendId);

        $entity->removeFromFriendList($friend->getId());
        $friend->removeFromFriendList($entity->getId());

        $this->getElastic()->persist($entity);
        $this->getElastic()->persist($friend);

        return $this->getUserService()->get($friendId);

    }
}