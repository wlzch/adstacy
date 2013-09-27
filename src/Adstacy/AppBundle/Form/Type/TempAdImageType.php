<?php

namespace Adstacy\AppBUndle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TempAdImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder->add('image', 'file', array(
            'label' => 'form_ad.image',
            'required' => true,
            'error_bubbling' => true
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Adstacy\AppBundle\Entity\TempAdImage',
                'csrf_protection' => false
            )
        );
    }

    public function getName()
    {
        return 'images';
    }
}
