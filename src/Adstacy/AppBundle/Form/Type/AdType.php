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
        $builder->add('image', 'image', array(
            'label' => 'form_ad.image',
            'required' => true
        ));
        $builder->add('description', 'textarea', array(
            'label' => 'form_ad.description',
            'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'Adstacy T-Shirt Rp250.000 #branded #tshirt #medan #cool'
            )
        ));
        $builder->add('content', 'textarea', array(
            'label' => 'form_ad.content',
            'required' => false,
            'attr' => array(
                'class' => 'tinymce'
            )
        ));
        $builder->add('save', 'submit', array(
            'label' => 'form_ad.submit'
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Adstacy\AppBundle\Entity\Ad'
            )
        );
    }

    public function getName()
    {
        return 'ad';
    }
}
