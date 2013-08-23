<?php

namespace Adstacy\AppBUndle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WallType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder->add('name', 'text', array(
            'label' => 'Name',
            'error_bubbling' => true,
            'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'Kemeja Pria'
            )
        ));
        $builder->add('description', 'textarea', array(
            'label' => 'Description',
            'error_bubbling' => true,
            'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'Semua #Kemeja #Pria ber#Kualitas'
            )
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Adstacy\AppBundle\Entity\Wall'
            )
        );
    }

    public function getName()
    {
        return 'wall';
    }
}
