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
            ->add('difficulty', 'choice', array(
                'choices' => Task::$difficulty_choices,
            ))
            ->add('priority', 'choice', array(
                'choices' => Task::$priority_choices,
            ))
            ->add('assignee', 'entity', array(
                'class' => 'AguilaBundle:User',
                'required' => false,
            ))
        ;
    }

    public function getName()
    {
        return 'task_form';
    }
}
