<?php

namespace Ads\AppBUndle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder->add('image', 'image', array(
            'label' => 'Image' 
        ));
        $builder->add('description', 'textarea', array(
            'label' => 'Description' 
        ));
        $builder->add('content', 'textarea', array(
            'label' => 'Content'
        ));
        $builder->add('post', 'submit', array(
            'label' => 'Post'
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Ads\AppBundle\Entity\Post'
            )
        );
    }

    public function getName()
    {
        return 'post';
    }
}
