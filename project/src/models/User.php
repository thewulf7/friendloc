<?php
namespace thewulf7\friendloc\models;

use thewulf7\friendloc\components\elasticsearch\annotations as ElasticSearch;
use thewulf7\friendloc\components\elasticsearch\Model;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 * @ElasticSearch\Entity(index="users", type="user", number_of_shards=5, number_of_replicas=1)
 */
class User implements Model
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @ElasticSearch\ElasticField(type="string", includeInAll=false)
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string",unique=TRUE)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     *
     * @ElasticSearch\ElasticField(type="string", includeInAll=true)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $salt;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $passwd;

    /**
     * @ORM\Column(type="string",length=40,nullable=TRUE)
     */
    private $userhash;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $approved;

    /**
     * @ORM\Column(type="datetime", name="created")
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @ElasticSearch\ElasticField(type="array", includeInAll=true)
     * @var array
     */
    private $friendList = [];

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name'       => $this->getName(),
            'friendList' => $this->getFriendList(),
        ];
    }

    /**
     * Get Id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param mixed $id
     *
     * @return User
     */
    public function setId($id): User
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get Email
     *
     * @return mixed
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param mixed $email
     *
     * @return User
     */
    public function setEmail($email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get Name
     *
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param mixed $name
     *
     * @return User
     */
    public function setName($name): User
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Salt
     *
     * @return mixed
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * Set salt
     *
     * @param mixed $salt
     *
     * @return User
     */
    public function setSalt($salt): User
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get Passwd
     *
     * @return mixed
     */
    public function getPasswd(): string
    {
        return $this->passwd;
    }

    /**
     * Set passwd
     *
     * @param mixed $passwd
     *
     * @return User
     */
    public function setPasswd($passwd): User
    {
        $this->passwd = $passwd;

        return $this;
    }

    /**
     * Get Approved
     *
     * @return mixed
     */
    public function getApproved(): boolean
    {
        return $this->approved;
    }

    /**
     * Set approved
     *
     * @param mixed $approved
     *
     * @return User
     */
    public function setApproved($approved): User
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get Created
     *
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return User
     */
    public function setCreated($created): User
    {
        $this->createdAt = $created;

        return $this;
    }

    /**
     * Get Userhash
     *
     * @return mixed
     */
    public function getUserhash(): string
    {
        return $this->userhash;
    }

    /**
     * Set userhash
     *
     * @param mixed $userhash
     *
     * @return User
     */
    public function setUserhash($userhash): User
    {
        $this->userhash = $userhash;

        return $this;
    }

    /**
     * Get FriendList
     *
     * @return array
     */
    public function getFriendList()
    {
        return $this->friendList;
    }

    /**
     * Set friendList
     *
     * @param array $friendList
     *
     * @return Friends
     */
    public function setFriendList($friendList)
    {
        $this->friendList = $friendList;

        return $this;
    }

    /**
     * Add item to friendList
     *
     * @param int $friendId
     *
     * @return $this
     */
    public function addToFriendList(int $friendId)
    {
        if (!in_array($friendId, $this->friendList, true))
        {
            $this->friendList[] = $friendId;
        }

        return $this;
    }
}