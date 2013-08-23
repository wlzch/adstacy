<?php

namespace Adstacy\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Adstacy\AppBundle\Form\DataTransformer\ImageToUploadedFileTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ImageToUploadedFileTransformer($this->om);
        $builder->addModelTransformer($transformer);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $form->getParent()->getData();
        $function = 'get'.$form->getName(); // entity field name
        $photo = $data->$function();
        $view->vars['image'] = $photo;
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
        return 'file';
    }

    public function getName()
    {
        return 'image';
    }
}
