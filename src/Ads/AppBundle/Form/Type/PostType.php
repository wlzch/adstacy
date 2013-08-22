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
        $username = $options['username'];
        $builder->add('wall', 'entity', array(
            'label' => 'Wall',
            'class' => 'AdsAppBundle:Wall',
            'query_builder' => function(EntityRepository $er) use (&$username) {
                return $er->createQueryBuilder('w')
                  ->innerJoin('w.user', 'u')
                  ->andWhere('u.usernameCanonical = :username')
                  ->setParameter('username', $username)
                ;
            }
        ));
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
        $resolver->setRequired(array('username'));
    }

    public function getName()
    {
        return 'post';
    }
}