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
            ->add('description')
            ->add('difficulty', 'choice', array(
                'choices' => Task::$difficulty_choices,
            ))
            ->add('priority', 'choice', array(
                'choices' => Task::$priority_choices,
            ))
            ->add('status', 'choice', array(
                'choices' => Task::$status_choices,
            ))
            ->add('assignee', array(
                'required' => false,
            ))
            ->add('reporter')
            ->add('comments', array(
                'required' => false,
            ))
            ->add('issues', array(
                'required' => false,
            ))
            ->add('feature')
        ;
    }

    public function getName()
    {
        return 'storm_aguilabundle_tasktype';
    }
}
