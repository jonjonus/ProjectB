<?php

namespace ProyB\DomainModelBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TransactionRepository extends EntityRepository
{
    public function selectCountByState(State $state)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT count(t)
                            FROM ProyBDomainModelBundle:Transaction t
                            WHERE t.state = :state
                            AND t.inactiveDate is null'
                        )->setParameter('state', $state)
                        ->getSingleScalarResult();
    }

    public function findActiveForUserPaginated($lastrow,$rpp,$user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->add('select', 't')
            ->add('from', 'ProyBDomainModelBundle:Transaction t')
            ->add('where', 't.inactiveDate is null')
            ->addOrderBy('t.date', 'DESC')
            ->addOrderBy('t.insertDate', 'DESC')
            ->setFirstResult($lastrow)//offset
            ->setMaxResults($rpp);
        if (!in_array('ROLE_ADMIN', $user->getRoles())){
            $qb->andWhere('t.state IN (:ids)')
                ->setParameter('ids', $user->getStates()->getValues());
        }
            return $qb->getQuery()->getResult();
    }
    
    public function findAllPaginated($lastrow,$rpp)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->add('select', 't')
            ->add('from', 'ProyBDomainModelBundle:Transaction t')
            ->addOrderBy('t.date', 'DESC')
            ->addOrderBy('t.insertDate', 'DESC')
            ->setFirstResult($lastrow)
            ->setMaxResults($rpp)
            ->getQuery()->getResult();
    }
}
