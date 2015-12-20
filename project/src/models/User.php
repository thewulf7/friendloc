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
     */
    private $id;

    /**
     * @Column(type="string")
     */
    private $email;

    /**
     * @Column(type="string")
     */
    private $name;

    /**
     * @Column(type="integer")
     */
    private $locationId;

    /**
     * @Column(type="string")
     */
    private $salt;

    /**
     * @Column(type="string")
     */
    private $passwd;

    /**
     * @Column(type="boolean")
     */
    private $approved;

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
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get Email
     *
     * @return mixed
     */
    public function getEmail()
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
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get Name
     *
     * @return mixed
     */
    public function getName()
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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get LocationId
     *
     * @return mixed
     */
    public function getLocationId()
    {
        return $this->locationId;
    }

    /**
     * Set locationId
     *
     * @param mixed $locationId
     *
     * @return User
     */
    public function setLocationId($locationId)
    {
        $this->locationId = $locationId;

        return $this;
    }

    /**
     * Get Salt
     *
     * @return mixed
     */
    public function getSalt()
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
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get Passwd
     *
     * @return mixed
     */
    public function getPasswd()
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
    public function setPasswd($passwd)
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
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }
}