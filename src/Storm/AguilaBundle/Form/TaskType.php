<?php

namespace Storm\AguilaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('difficulty')
            ->add('priority')
            ->add('assignee')
            ->add('reporter')
            ->add('comments')
            ->add('issues')
            ->add('feature')
        ;
    }

    public function getName()
    {
        return 'storm_aguilabundle_tasktype';
    }
}
