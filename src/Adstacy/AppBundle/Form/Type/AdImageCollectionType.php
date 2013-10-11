<?php

namespace Adstacy\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Adstacy\AppBundle\Form\DataTransformer\ImagesToIdsTransformer;

class AdImageCollectionType extends AbstractType
{
    private $transformer;

    public function __construct(ImagesToIdsTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $form->getParent()->getData();
        $view->vars['obj'] = $data;
    }

    public function getParent()
    {
        return 'hidden';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'An error occured. please retry'
        ));
    }

    public function getName()
    {
        return 'ad_images';
    }
}
