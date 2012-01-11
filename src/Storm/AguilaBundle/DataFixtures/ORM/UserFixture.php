<?php
namespace Storm\AguilaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\UserBundle\Util\UserManipulator;
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
    public function load($manager)
    {
        /* @var $manipulator UserManipulator */
        $manipulator = $this->container->get('fos_user.util.user_manipulator');

        $manipulator->create(
            'admin',
            'admin',
            'admin@aguila.sf',
            true,
            true);

        for ($i = 0; $i < 3; $i++) {
            $manipulator->create(
                'user' . $i,
                'user' . $i,
                'user' . $i . '@aguila.sf',
                true,
                false);
        }
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
