<?php
namespace thewulf7\friendloc\models;

use Ivory\GoogleMap\Base\Coordinate;
use thewulf7\friendloc\components\elasticsearch\annotations as ElasticSearch;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 * @ElasticSearch\Entity(index="users", type="user", number_of_shards=1, number_of_replicas=1, autocomplete=true)
 */
class User implements \JsonSerializable
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
     * @ElasticSearch\ElasticField(type="boolean", includeInAll=false)
     * @var boolean
     */
    private $approved;

    /**
     * @ORM\Column(type="datetime", name="created")
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @ElasticSearch\ElasticField(type="integer", includeInAll=true)
     * @var array
     */
    private $friendList = [];

    /**
     * @ElasticSearch\ElasticField(type="string", includeInAll=true)
     * @var string
     */
    private $locationName;

    /**
     * @ElasticSearch\ElasticField(type="geo_point")
     * @var Coordinate
     */
    private $latlng;

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id'           => $this->getId(),
            'name'         => $this->getName(),
            'email'        => $this->getEmail(),
            'sign'         => $this->getSign(),
            'link'         => '/#/users/' . $this->getId(),
            'friendList'   => $this->getFriendList(),
            'locationName' => $this->getLocationName(),
            'approved'     => $this->getApproved(),
            'latlng'       => $this->getLatlng() ? [
                'lat' => (double)$this->getLatlng()->getLatitude(),
                'lon' => (double)$this->getLatlng()->getLongitude(),
            ] : null,
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
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $this->email = $email;
        } else
        {
            throw new \InvalidArgumentException('Wrong email');
        }

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
    public function getApproved()
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
     * @return User
     */
    public function setFriendList(array $friendList): User
    {
        $this->friendList = array_map('intval',$friendList);

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
            $this->friendList[] = (int)$friendId;
        }

        return $this;
    }

    /**
     * @param int $friendId
     *
     * @return User
     */
    public function removeFromFriendList(int $friendId)
    {
        if (in_array($friendId, $this->friendList, true))
        {
            $key = array_search($friendId, $this->friendList, true);
            unset($this->friendList[$key]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSign()
    {
        $name = explode(' ', $this->getName());
        $sign = count($name) > 1 ? $name[0][0] . $name[1][0] : $name[0][0] . $name[0][1];

        return strtoupper($sign);
    }

    /**
     * Get Latlng
     *
     * @return Coordinate
     */
    public function getLatlng()
    {
        return $this->latlng;
    }

    /**
     * Set latlng
     *
     * @param array $latlng
     *
     * @return User
     */
    public function setLatlng(array $latlng): User
    {
        list($latitude, $longitude) = array_values($latlng);

        $this->latlng = new Coordinate($latitude, $longitude);

        return $this;
    }

    /**
     * Get LocationName
     *
     * @return string
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     * Set locationName
     *
     * @param string $locationName
     *
     * @return User
     */
    public function setLocationName($locationName): User
    {
        $this->locationName = $locationName;

        return $this;
    }
}