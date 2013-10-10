<?php

namespace Adstacy\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Wysihtml5Type extends AbstractType
{
    public function getParent()
    {
        return 'purified_textarea';
    }

    public function getName()
    {
        return 'wysihtml5';
    }
}
