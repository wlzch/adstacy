<?php

namespace Adstacy\AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ReportAdAdmin extends Admin
{   
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('ad')
            ->add('user')
            ->add('reportedAt')
            ->add('status')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('status')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('ad')
            ->add('user')
            ->add('reportedAt')
            ->add('status')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'view' => array(
                        'template' => 'AdstacyAppBundle:Admin:view_ad.html.twig'
                    )
                )
            ))
        ;
    }
}
