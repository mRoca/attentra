<?php

namespace Attentra\TimeBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TimeInputRepository extends EntityRepository
{

    /**
     * @param string $identifier
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function qbFindByIdentifier($identifier = '')
    {
        $qb = $this->createQueryBuilder('t');

        if ($identifier) {
            $qb->where($qb->expr()->eq('t.identifier', ':identifier'));
            $qb->setParameter('identifier', $identifier);
        }

        return $qb;
    }

    /**
     * @param string $identifier
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function qbFindByDates($identifier = '', $startDate = null, $endDate = null)
    {
        $qb = $this->qbFindByIdentifier($identifier);

        if ($startDate !== null) {
            $qb->andWhere($qb->expr()->gte('t.datetime', ':startDate'))->setParameter('startDate', $startDate);
        }

        if ($endDate !== null) {
            $qb->andWhere($qb->expr()->lte('t.datetime', ':endDate'))->setParameter('endDate', $endDate);
        }

        return $qb;
    }
} 
