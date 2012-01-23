<?php

namespace Storm\AguilaBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Storm\AguilaBundle\Entity\Task;

/**
 * TaskRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TaskRepository extends EntityRepository
{
    public function findOneByProject($project_slug, $number)
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.feature', 'f')
            ->leftJoin('f.project', 'p')
            ->where('t.number = :number')
            ->andWhere('p.slug = :project_slug')
            ->setParameters(array(
                'number' => $number,
                'project_slug' => $project_slug,
            ));
        return $qb->getQuery()->getSingleResult();
    }

    public function findOpenByFeature($feature_slug)
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.feature', 'f')
            ->where('f.slug = :feature_slug')
            ->andWhere('t.status = :status')
            ->setParameters(array(
                'feature_slug' => $feature_slug,
                'status' => Task::OPEN,
        ));
        return $qb->getQuery()->getResult();
    }
}