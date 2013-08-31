<?php

namespace Adstacy\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {

        $builder->add('subject', 'text', array(
            'label' => 'form_contact.subject',
            'required' => false
        ));
        $builder->add('email', 'email', array(
            'label' => 'form_contact.email'
        ));
        $builder->add('content', 'textarea', array(
            'label' => 'form_contact.content',
            'attr' => array(
                'rows' => 5
            )
        ));
        $builder->add('submit','submit', array(
            'label' => 'form_contact.submit'
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Adstacy\AppBundle\Model\Contact'
        ));
    }

    public function getName()
    {
        return 'contact';
    }
}
