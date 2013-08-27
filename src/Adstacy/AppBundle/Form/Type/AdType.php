<?php

namespace Adstacy\AppBUndle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $username = $options['username'];
        $builder->add('wall', 'entity', array(
            'label' => 'Wall',
            'class' => 'AdstacyAppBundle:Wall',
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
            'label' => 'Description',
            'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'Adstacy T-Shirt Rp250.000 #branded #tshirt #medan #cool'
            )
        ));
        $builder->add('content', 'textarea', array(
            'label' => 'Content',
            'attr' => array(
                'class' => 'tinymce'
            )
        ));
        $builder->add('save', 'submit', array(
            'label' => 'Save'
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Adstacy\AppBundle\Entity\Ad'
            )
        );
        $resolver->setRequired(array('username'));
    }

    public function getName()
    {
        return 'ad';
    }
}
