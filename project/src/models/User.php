<?php
namespace thewulf7\friendloc\models;


use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity
 * @Table(name="users")
 */
class User
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(type="string",unique=TRUE)
     * @var string
     */
    private $email;

    /**
     * @Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @Column(type="string")
     * @var string
     */
    private $salt;

    /**
     * @Column(type="string")
     * @var string
     */
    private $passwd;

    /**
     * @Column(type="string",length=40,nullable=TRUE)
     */
    private $userhash;

    /**
     * @Column(type="boolean")
     * @var boolean
     */
    private $approved;

    /**
     * @Column(type="datetime", name="created")
     * @var \DateTime
     */
    private $createdAt;

    /**
     * Get Id
     *
     * @return mixed
     */
    public function getId(): string
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
}