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
        $builder->add('image', 'file', array(
            'label' => 'form_ad.image',
            'required' => false,
            'error_bubbling' => true
        ));
        $builder->add('title', 'text', array(
            'label' => 'form_ad.title',
            'required' => false,
            'error_bubbling' => true
        ));
        $builder->add('imagename', 'hidden', array(
            'required' => false
        ));
        $builder->add('images', 'ad_images', array(
            'label' => 'form_ad.images',
            'required' => false,
            'error_bubbling' => true
        ));
        $builder->add('description', 'wysihtml5', array(
            'label' => 'form_ad.description',
            'required' => false,
            'attr' => array(
                'placeholder' => 'form_ad.placeholders.description'
            ),
            'error_bubbling' => true
        ));
        $builder->add('tags', 'hashtags', array(
            'label' => 'form_ad.hashtags',
            'error_bubbling' => true
        ));
        $builder->add('type', 'hidden', array(
            'required' => false,
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
