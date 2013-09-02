<?php

namespace Adstacy\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $form->getParent()->getData();
        $function = 'get'.$form->getName(); // entity field name
        $photo = $data->$function();
        $view->vars['obj'] = $data;
        $view->vars['image_size'] = $options['image_size'];
        $view->vars['image_required'] = $options['image_required'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'An error occured. please retry',
            'required' => false,
            'image_size' => 'medium',
            'image_required' => true
        ));
    }

    public function getParent()
    {
        return 'file';
    }

    public function getName()
    {
        return 'image';
    }
}
