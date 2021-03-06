<?php

namespace AppBundle\Repository;

/**
 * BudgetPositionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BudgetPositionRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByMonthAndCategory($month, $year, $category)
    {
        $em = $this->getEntityManager();

        $budgetPossitions = $em->createQuery(
            'SELECT b FROM AppBundle:BudgetPosition b WHERE b.category=:category AND b.date LIKE :prefix ORDER BY b.id DESC')
            ->setParameter('category', $category)
            ->setParameter('prefix', $year.'-'.$month.'%')->getResult();

        return $budgetPossitions;
    }
}
