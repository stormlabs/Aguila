<?php
namespace Storm\AguilaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

/*
 * Base Controller class for AguilaBundle Controllers
 */
class Controller extends BaseController
{
    /**
     * Throws an error if user doesn't have access of type $mask to $object
     *
     * @param $mask
     * @param $object
     * @throws \InvalidArgumentException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function checkAccess($mask, $object)
    {
        /** @var $securityContext \Symfony\Component\Security\Core\SecurityContext */
        $securityContext = $this->get('security.context');
        $objectIdentity = new ObjectIdentity($object->getId(), $this->getClassSansProxy($object));
        if (false === $securityContext->isGranted($mask, $objectIdentity))
        {
            throw new AccessDeniedException(sprintf(
                'Access Denied for user "%s" to object(%s): %s with mask "%s"',
                (string)$securityContext->getToken()->getUserName(),
                $this->getClassSansProxy($object),
                (string)$object,
                $mask
            ));
        }
    }

    /**
     * Grants a user access to an object
     *
     * @param $mask
     * @param $object
     * @param bool $newObject whether the $object is new (and doesn't have acl yet) or not
     * @param null|\Symfony\Component\Security\Core\User\UserInterface $user
     */
    protected function grantAccess($mask, $object, $newObject=false, UserInterface $user=null)
    {
        /** @var $aclProvider \Symfony\Component\Security\Acl\Dbal\MutableAclProvider */
        $aclProvider = $this->get('security.acl.provider');
        $objectIdentity = new ObjectIdentity($object->getId(), $this->getClassSansProxy($object));
        $method = $newObject ? 'createAcl' : 'findAcl';
        $acl = $aclProvider->{$method}($objectIdentity);

        $securityContext = $this->get('security.context');
        $user = (null !== $user)? $user : $securityContext->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        /** @var $acl \Symfony\Component\Security\Acl\Domain\Acl */
        $acl->insertObjectAce($securityIdentity, $mask);
        $aclProvider->updateAcl($acl);
    }

    /**
     * Returns the class name without proxies
     *
     * @param $object
     * @return string
     */
    protected function getClassSansProxy($object)
    {
        return ($object instanceof \Doctrine\ORM\Proxy\Proxy) ? get_parent_class($object) : get_class($object);
    }
}
