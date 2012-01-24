<?php

namespace Storm\AguilaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FeatureRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeatureRepository extends EntityRepository
{
    /**
     * @param $project_slug
     * @param $feature_slug
     * @return Feature
     */
    public function findFeatureBySlugs($project_slug, $feature_slug)
    {
        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.project', 'p')
            ->where('f.slug = :feature_slug')
            ->andWhere('p.slug = :project_slug')
            ->setParameters(array(
            'feature_slug' => $feature_slug,
            'project_slug' => $project_slug,
        ));
        return $qb->getQuery()->getSingleResult();
    }
}
