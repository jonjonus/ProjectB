<?php

namespace ProyB\DomainModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Transaction
{
    private $id;
    private $insertDate;
    private $insertUser;
    private $updateDate;
    private $updateUser;
    private $date;
    private $amount;
    private $state;
    private $comment;
    private $inactiveDate;
    private $inactiveUser;
    
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setInsertDate($insertDate)
    {
        $this->insertDate = $insertDate;

        return $this;
    }

    public function getInsertDate()
    {
        return $this->insertDate;
    }

    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    public function setInactiveDate($inactiveDate)
    {
        $this->inactiveDate = $inactiveDate;

        return $this;
    }

    public function getInactiveDate()
    {
        return $this->inactiveDate;
    }

    public function setId($id)
    {
        return $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setInsertUser(\ProyB\DomainModelBundle\Entity\User $insertUser = null)
    {
        $this->insertUser = $insertUser;

        return $this;
    }

    public function getInsertUser()
    {
        return $this->insertUser;
    }

    public function setUpdateUser(\ProyB\DomainModelBundle\Entity\User $updateUser = null)
    {
        $this->updateUser = $updateUser;

        return $this;
    }

    public function getUpdateUser()
    {
        return $this->updateUser;
    }

    public function setInactiveUser(\ProyB\DomainModelBundle\Entity\User $inactiveUser = null)
    {
        $this->inactiveUser = $inactiveUser;

        return $this;
    }

    public function getInactiveUser()
    {
        return $this->inactiveUser;
    }

    public function setState(\ProyB\DomainModelBundle\Entity\State $state = null)
    {
        $this->state = $state;

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }
}
