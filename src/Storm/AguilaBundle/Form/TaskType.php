<?php

namespace Storm\AguilaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Storm\AguilaBundle\Entity\Task;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('difficulty', null, array(
                'attr' => array('min' => 0, 'max' => 100)
            )
        )
            ->add('priority', null, array(
                'attr' => array('min' => 0, 'max' => 100)
            )
        )
            ->add('assignee', 'entity', array(
                'class' => 'AguilaBundle:User',
                'required' => false,
            )
        );
        if ($options['data']->getId() !== null) {
            $builder
                ->add('feature')
                ->add('status', 'choice', array(
                    'choices' => Task::getStatusChoices(),
                )
            );
        }
    }

    public function getName()
    {
        return 'task_form';
    }
}
