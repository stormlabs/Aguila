<?php
namespace Storm\AguilaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Storm\AguilaBundle\Entity\Feature;

class FeatureFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param object $manager
     */
    function load($manager)
    {
        foreach (range(0, 10) as $n) {
            $feature = new Feature();
            $feature->setName("feature-".$n);
            $feature->setDescription("feature-".$n);
            $feature->setProject($this->getReference('project-salgamos'));
            $this->addReference('feature-'.$n, $feature);
            $manager->persist($feature);
        }
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 3;
    }

}
