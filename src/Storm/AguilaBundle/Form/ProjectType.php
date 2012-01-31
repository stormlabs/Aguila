<?php

namespace Storm\AguilaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
        ;
    }

    public function getName()
    {
        return 'project_form';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'intention' => 'project',
        );
    }
}
