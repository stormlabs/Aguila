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
        $project1 = new Project();
        $project1->setName('Salgamos');
        $manager->persist($project1);

        $project2 = new Project();
        $project2->setName('Aguila');
        $manager->persist($project2);

        $manager->flush();

        $this->addReference('project-salgamos', $project1);
        $this->addReference('project-aguila', $project2);
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
