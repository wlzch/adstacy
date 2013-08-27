<?php

namespace Adstacy\AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class UserAdmin extends Admin
{   
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('username')
            ->add('email')
            ->add('enabled', 'checkbox', array(
                'required' => false 
            ))
            ->add('locked', 'checkbox', array(
                'required' => false
            ))
            ->add('roles', 'choice', array(
                'choices' => array(
                    'ROLE_USER' => 'User',
                    'ROLE_SUPER_ADMIN' => 'Super Admin'
                ),
                'multiple' => true,
                'expanded' => true
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username')
            ->add('email')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('username')
            ->add('email')
            ->add('enabled')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'impersonate' => array(
                        'template' => 'AdstacyAppBundle:Admin:user_actions.html.twig' 
                    ) 
                )
            ))
        ;
    }
}
