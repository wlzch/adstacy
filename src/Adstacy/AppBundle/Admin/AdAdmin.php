<?php

namespace Adstacy\AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AdAdmin extends Admin
{   
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('description', 'textarea')
            ->add('content', 'textarea', array(
                'attr' => array(
                    'class' => 'tinymce'
                )
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('description')
            ->add('user.username', null, array('label' => 'Username'))
            ->add('promoteesCount')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('description')
            ->add('user.username', null, array('label' => 'Username'))
            ->add('promoteesCount')
        ;
    }
}
