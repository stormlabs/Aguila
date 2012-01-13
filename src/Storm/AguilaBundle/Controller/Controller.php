<?php
namespace Storm\AguilaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/*
 * Base Controller class for AguilaBundle Controllers
 */
class Controller extends BaseController
{
    /*
     * Throws an error if user doesn't have access of $type to $object
     */
    protected function checkAccess($type, $object)
    {
        $securityContext = $this->get('security.context');
        $className = ($object instanceof \Doctrine\ORM\Proxy\Proxy) ? get_parent_class($object) : get_class($object);
        $objectIdentity = new ObjectIdentity($object->getId(), $className);
        if (false === $securityContext->isGranted($type, $objectIdentity))
        {
            throw new AccessDeniedException();
        }
    }

    protected function grantAccess($type, $object)
    {
        $aclProvider = $this->get('security.acl.provider');
        $className = ($object instanceof \Doctrine\ORM\Proxy\Proxy) ? get_parent_class($object) : get_class($object);
        $objectIdentity = new ObjectIdentity($object->getId(), $className);
        $acl = $aclProvider->createAcl($objectIdentity);

        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $acl->insertObjectAce($securityIdentity, $type);
        $aclProvider->updateAcl($acl);
    }
}
