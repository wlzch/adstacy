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
            'required' => true,
            'error_bubbling' => true
        ));
        $builder->add('description', 'purified_textarea', array(
            'label' => 'form_ad.description',
            'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'form_ad.placeholders.description'
            ),
            'error_bubbling' => true
        ));
        $builder->add('content', 'purified_textarea', array(
            'label' => 'form_ad.content',
            'required' => false,
            'attr' => array(
                'class' => 'tinymce'
            ),
            'error_bubbling' => true
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
