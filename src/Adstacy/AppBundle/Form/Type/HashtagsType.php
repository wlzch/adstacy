<?php

namespace Adstacy\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Adstacy\AppBundle\Form\DataTransformer\HashtagToArrayTransformer;

class HashtagsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new HashtagToArrayTransformer();
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'An error occured. please retry',
            'required' => false
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'hashtags';
    }
}
