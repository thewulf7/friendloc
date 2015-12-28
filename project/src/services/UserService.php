<?php
namespace thewulf7\friendloc\services;


use DI\NotFoundException;
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
     * Create new user
     *
     * @param        $email
     * @param        $name
     * @param        $passwd
     *
     * @return array
     */
    public function create(string $email, string $name, string $passwd, string $salt, string $locationName, array $latlng): User
    {
        $entityManager = $this->getEntityManager();

        $model = new User();
        $model
            ->setEmail($email)
            ->setName($name)
            ->setPasswd(Auth::createPassword($passwd, $salt))
            ->setSalt($salt)
            ->setApproved(false)
            ->setCreated(new \DateTime("now"))
            ->setLatlng($latlng)
            ->setLocationName($locationName);

        $entityManager->persist($model);

        $this->getElastic()->persist($model);

        $hash = hash('sha1', time() . '|' . $model->getId());
        $model->setUserhash($hash);

        $entityManager->persist($model);
        $entityManager->flush();

        return $model;
    }

    /**
     * Get User model by ID
     *
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
        /** @var ElasticSearch $service */
        $service = $this->getElastic();

        /** @var User $elModel */
        $elModel = $service->find('User', $id);
        /** @var User $eModel */
        $eModel = $entityManager->find('thewulf7\friendloc\models\User', $id);

        if (!$elModel)
        {
            throw new NotFoundException('User with id `' . $id . '` not found');
        }

        $latlng = $elModel->getLatlng() ? [$elModel->getLatlng()->getLatitude(), $elModel->getLatlng()->getLongitude()] : null;
        $eModel->setLocationName($elModel->getLocationName());
        $eModel->setLatlng($latlng);
        $eModel->setFriendList($elModel->getFriendList());

        return $eModel;
    }

    /**
     * Delete user by ID
     *
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
     * Update user
     *
     * @param int    $id
     * @param string $name
     * @param string $password
     *
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function update(int $id, string $name = '', string $email, string $password = '', string $locationName = '', array $location = []): User
    {
        $entityManager = $this->getEntityManager();
        $model         = $this->get($id);

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
            $model->setPasswd($password);
        }

        if (strlen($locationName) > 0)
        {
            $model->setLocationName($locationName);
        }

        if (count($location) > 0)
        {
            $model->setLatlng($location);
        }


        $entityManager->persist($model);
        $entityManager->flush();

        $this->getElastic()->persist($model);

        return $model;
    }

    /**
     * Get user friends by id
     *
     * @param int $userId
     *
     * @return array
     */
    public function getFriends(int $userId): array
    {
        /** @var User $entity */
        $entity = $this->get($userId);

        return array_map(function ($friendId)
        {
            return $this->get($friendId);
        }, $entity->getFriendList());
    }

    /**
     * Add to friends by ID
     *
     * @param int $userId
     * @param int $friendId
     *
     * @return mixed
     */
    public function addToFriends(int $userId, int $friendId)
    {
        /** @var User $entity */
        $entity = $this->get($userId);
        /** @var User $friend */
        $friend = $this->get($friendId);

        $entity->addToFriendList($friend->getId());
        $friend->addToFriendList($entity->getId());

        $this->getElastic()->persist($entity);
        $this->getElastic()->persist($friend);

        return $this->getUserService()->get($friendId);

    }

    /**
     * Remove from friends by ID
     *
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