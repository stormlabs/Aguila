<?php

namespace Storm\AguilaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class ProjectAdminType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('user', 'entity', array(
                'class' => 'AguilaBundle:User',
                'required' => false,
            ))
            ->add('permission', 'choice', array(
                'choices' => array(
                    MaskBuilder::MASK_VIEW => 'view',
                    MaskBuilder::MASK_EDIT => 'edit',
                )
            ))
        ;
    }

    public function getName()
    {
        return 'project_admin_form';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'intention' => 'project_admin',
        );
    }
}
