<?php

namespace ProyB\DomainModelBundle\Entity;

use Doctrine\ORM\EntityRepository;

class StateRepository extends EntityRepository
{
    public function buildQueryAllStates()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb ->select('s')
            ->from('ProyBDomainModelBundle:State', 's')
            ->orderBy('s.description', 'ASC');
        return $qb;
    }
    
    public function buildQueryStatesForUser()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb ->select('s')
            ->from('ProyBDomainModelBundle:State', 's')
            ->where('s.id > 0')
            ->orderBy('s.description', 'ASC');
        return $qb;
    }
}
