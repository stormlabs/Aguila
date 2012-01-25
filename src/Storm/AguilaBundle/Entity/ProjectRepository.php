<?php

namespace Storm\AguilaBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjectRepository extends EntityRepository
{

    /**
     * @param User $user
     * @return array
     */
    public function findAcesForUser(User $user)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('class_type', 'class_type');
        $rsm->addScalarResult('object_identifier', 'object_identifier');
        $rsm->addScalarResult('mask', 'mask');

        $query = $this->_em->createNativeQuery(
                'SELECT c.class_type, oid.object_identifier, e.mask '.
                'FROM acl_security_identities sid '.
                'JOIN acl_entries e ON sid.id = e.security_identity_id '.
                'JOIN acl_object_identities oid ON (e.class_id = oid.class_id AND (e.object_identity_id = oid.id OR e.object_identity_id IS NULL)) '.
                'JOIN acl_classes c ON oid.class_id = c.id '.
                'WHERE sid.username = :username'
            , $rsm);
        $query->setParameter('username', $user->getId());

        $result = array();
        foreach ($query->getResult() as $i => $row)
        {
            $result[$i]['objectType']       = $row['class_type'];
            $result[$i]['objectIdentifier'] = $row['object_identifier'];
            $result[$i]['permissions']      = MaskBuilder::analyzeMask($row['mask']);
        }

        return $result;
    }

}