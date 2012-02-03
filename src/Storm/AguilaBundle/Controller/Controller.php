<?php
namespace Storm\AguilaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

/**
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
        $this->get('storm.aguila.aclmanager')->checkAccess($mask, $object);
    }

    /**
     * Grants a user access to an object
     *
     * @param $mask
     * @param $object
     * @param bool $newObject whether the $object is new (and doesn't have acl yet) or not
     * @param null|\Symfony\Component\Security\Core\User\UserInterface|string $user
     */
    protected function grantAccess($mask, $object, $newObject=false, $user=null)
    {
        $this->get('storm.aguila.aclmanager')->grantAccess($mask, $object, $newObject, $user);
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
