<?php
namespace Storm\AguilaBundle\DataFixtures\ORM\Prod;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Storm\AguilaBundle\Entity\User;

class UserFixture extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param object $manager
     */
    public function load(ObjectManager $manager)
    {
        /* @var $manipulator \FOS\UserBundle\Util\UserManipulator */
        $manipulator = $this->container->get('fos_user.util.user_manipulator');

        $manipulator->create(
            'admin',
            'admin',
            'ejosblog@gmail.com',
            true,
            true);
        $manipulator->promote('admin');
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 1;
    }

}
