<?php
namespace Storm\AguilaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Storm\AguilaBundle\Entity\Task;

class TaskFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function getDescCommand()
    {
        return "fortune";
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param object $manager
     */
    function load($manager)
    {
        $fortune = true;
        exec($this->getDescCommand(), $output);
        if(!$output) {
            $fortune = false;
        }


        foreach (range(0, 100) as $n) {
            $task = new Task();
            $feature = $this->getReference("feature-".($n%11));
            $task->setFeature($feature);
            $task->setTitle(substr($this->getTitle(), 0, 40));
            $task->setDifficulty(($n+10) % 4);
            $task->setPriority($n % 4);
            $user = $manager->getRepository('AguilaBundle:User')->findOneBy(array('username' => 'admin'));
            $task->setReporter($user);
            $task->setCreatedAt(new \DateTime('now + '.$n.'days - '.$n.'months'));
            $number = $this->getReference("project-salgamos")->getId().$n;
            $task->setNumber((int) $number);

            $manager->persist($task);
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
        return 4;
    }

    private function getTitle()
    {
        exec($this->getDescCommand(), $output);
        $fortune = trim(implode("\n", $output));
        return $fortune;
    }

}
