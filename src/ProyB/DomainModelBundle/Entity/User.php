<?php

namespace ProyB\DomainModelBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

class User implements UserInterface, \Serializable
{
    private $id;
    private $username;
    private $password;
    private $name;
    private $email;
    private $states;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        //$this->salt = md5(uniqid(null, true));
        $this->states = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add states
     *
     * @param \ProyB\DomainModelBundle\Entity\State $states
     * @return User
     */
    public function addState(\ProyB\DomainModelBundle\Entity\State $states)
    {
        $this->states[] = $states;

        return $this;
    }

    /**
     * Remove states
     *
     * @param \ProyB\DomainModelBundle\Entity\State $states
     */
    public function removeState(\ProyB\DomainModelBundle\Entity\State $states)
    {
        $this->states->removeElement($states);
    }

    /**
     * Get states
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStates()
    {
        return $this->states;
    }

    public function eraseCredentials() {
        
    }

    public function getRoles() {
        return array('ROLE_USER');
    }

    public function getSalt() {
        return null;
    }
    
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->name,
            //$this->salt,
        ));
    }
    
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->name,
            //$this->salt
        ) = unserialize($serialized);
    }
}
