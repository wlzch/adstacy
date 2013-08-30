<?php

namespace Adstacy\AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class FeaturedAdAdmin extends Admin
{   
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('ad', 'sonata_type_model_list')
            ->add('fromDate')
            ->add('toDate')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('ad')
            ->add('fromDate')
            ->add('toDate')
        ;
    }
}
