<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adstacy\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends AbstractType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                    'label' => 'form.username',
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => array(
                        'class' => 'form-control input-lg',
                        'placeholder' => 'Username'
                    ),
                    'label_attr' => array(
                        'class' => 'hide'
                    )
                )
            )
            ->add('email', 'email', array(
                    'label' => 'form.email',
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => array(
                        'class' => 'form-control input-lg',
                        'placeholder' => 'email@example.com'
                    ),
                    'label_attr' => array(
                        'class' => 'hide'
                    )
                )
            )
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array(
                    'translation_domain' => 'FOSUserBundle'
                ),
                'first_options' => array(
                    'label' => 'form.password',
                    'attr' => array(
                        'class' => 'form-control input-lg',
                        'placeholder' => 'Password'
                    ),
                    'label_attr' => array(
                        'class' => 'hide'
                    )
                ),
                'second_options' => array(
                    'label' => 'form.password_confirmation',
                    'attr' => array(
                        'class' => 'form-control input-lg',
                        'placeholder' => 'Confirm Password'
                    ),
                    'label_attr' => array(
                        'class' => 'hide'
                    )
                ),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('register', 'submit', array(
                'label' => 'Register',
                'attr' => array(
                    'class' => 'btn btn-primary btn-block btn-lg'
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'registration',
        ));
    }

    public function getName()
    {
        return 'adstacy_registration';
    }
}