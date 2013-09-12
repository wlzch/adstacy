<?php

namespace Adstacy\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {

        $builder->add('content', 'purified_textarea', array(
            'label' => 'form_comment.content',
            'required' => true,
            'attr' => array(
                'placeholder' => 'Write a comment...'
            )
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Adstacy\AppBundle\Entity\Comment',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'comment';
    }
}
