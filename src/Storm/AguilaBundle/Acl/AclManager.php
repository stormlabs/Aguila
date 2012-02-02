<?php

namespace Storm\AguilaBundle\Acl;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

class AclManager
{
    protected $securityContext;
    protected $aclProvider;

    function __construct(SecurityContextInterface $securityContext, AclProviderInterface $aclProvider)
    {
        $this->securityContext = $securityContext;
        $this->aclProvider = $aclProvider;
    }

    /**
     * Throws an error if user doesn't have access of type $mask to $object
     *
     * @param $mask
     * @param $object
     * @throws \InvalidArgumentException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function checkAccess($mask, $object)
    {
        $objectIdentity = new ObjectIdentity($object->getId(), $this->getClassSansProxy($object));
        if (false === $this->securityContext->isGranted($mask, $objectIdentity))
        {
            throw new AccessDeniedException(sprintf(
                'Access Denied for user "%s" to object(%s): %s with mask "%s"',
                (string)$this->securityContext->getToken()->getUserName(),
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
     * @param null|\Symfony\Component\Security\Core\User\UserInterface|string $user
     */
    public function grantAccess($mask, $object, $newObject=false, $user=null)
    {
        $objectIdentity = new ObjectIdentity($object->getId(), $this->getClassSansProxy($object));
        $method = $newObject ? 'createAcl' : 'findAcl';
        $acl = $this->aclProvider->{$method}($objectIdentity);

        $user = (null !== $user)? $user : $this->securityContext->getToken()->getUser();

        if ($user instanceof UserInterface){
            $securityIdentity = UserSecurityIdentity::fromAccount($user);
        } elseif (is_string($user)) {
            $securityIdentity = new UserSecurityIdentity($user, 'Storm\AguilaBundle\Entity\User');
        } else {
            throw new \InvalidArgumentException('$user has to be null, a UserInterface or a username.');
        }

        /** @var $acl \Symfony\Component\Security\Acl\Domain\Acl */
        $acl->insertObjectAce($securityIdentity, $mask);
        $this->aclProvider->updateAcl($acl);
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
