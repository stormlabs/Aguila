<?php

namespace Storm\AguilaBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Storm\AguilaBundle\Entity\User
 *
 * @ORM\Table(name="aguila_user")
 * @ORM\Entity(repositoryClass="Storm\AguilaBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected  $id;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getGravatar()
    {
        return md5($this->email);
    }
}
