<?php

namespace ProyB\DomainModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class State
{
    private $id;
    private $description;
    private $trxs;
    private $users;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->trxs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set description
     *
     * @param string $description
     * @return State
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
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
     * Add trxs
     *
     * @param \ProyB\DomainModelBundle\Entity\Transaction $trxs
     * @return State
     */
    public function addTrx(\ProyB\DomainModelBundle\Entity\Transaction $trxs)
    {
        $this->trxs[] = $trxs;

        return $this;
    }

    /**
     * Remove trxs
     *
     * @param \ProyB\DomainModelBundle\Entity\Transaction $trxs
     */
    public function removeTrx(\ProyB\DomainModelBundle\Entity\Transaction $trxs)
    {
        $this->trxs->removeElement($trxs);
    }

    /**
     * Get trxs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTrxs()
    {
        return $this->trxs;
    }

    /**
     * Add users
     *
     * @param \ProyB\DomainModelBundle\Entity\User $users
     * @return State
     */
    public function addUser(\ProyB\DomainModelBundle\Entity\User $users)
    {
        $this->users[] = $users;
        // los Users estan "inversionados?"* por el State por eso hay que hacelo así
        // *en la BD el estado no actualiza no actualiza la tabla tbl_users_states
        // el "dueño" de la relación es el usuario.
        foreach ($this->users as $user){
            $user->addState($this);
        };
        return $this;
        //TODO esto no funciona xq hace un INSERT en la BD y deberia ser un UPDATE o BORRAR todo y luego INSERT
    }

    /**
     * Remove users
     *
     * @param \ProyB\DomainModelBundle\Entity\User $users
     */
    public function removeUser(\ProyB\DomainModelBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}
