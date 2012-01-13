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
        // if the user has permission to edit the project
        if (false === $securityContext->isGranted($type, $object))
        {
            throw new AccessDeniedException();
        }
    }

    protected function grantAccess($type, $object)
    {
        $aclProvider = $this->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $acl = $aclProvider->createAcl($objectIdentity);

        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $acl->insertObjectAce($securityIdentity, $type);
        $aclProvider->updateAcl($acl);
    }
}
