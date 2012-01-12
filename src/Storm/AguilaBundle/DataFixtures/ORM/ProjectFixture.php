<?php
namespace Storm\AguilaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Storm\AguilaBundle\Entity\Project;

class ProjectFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param object $manager
     */
    function load($manager)
    {
//        $project = new Project();
//        $project->setName('Salgamos');
//        $manager->persist($project);
//        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 2;
    }

}
