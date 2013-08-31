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
            ->add('email', 'email', array(
                'label' => 'form.email',
                'translation_domain' => 'FOSUserBundle',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'email@example.com'
                )
            ))
            ->add('username', 'text', array(
                'label' => 'form.username',
                'translation_domain' => 'FOSUserBundle',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Username'
                )
            ))
            ->add('realName', 'text', array(
                'label' => 'form_registration.fullname',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Fullname'
                )
            ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array(
                    'translation_domain' => 'FOSUserBundle'
                ),
                'first_options' => array(
                    'label' => 'form.password',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Password'
                    )
                ),
                'second_options' => array(
                    'label' => 'form.password_confirmation',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Confirm Password'
                    )
                ),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('register', 'submit', array(
                'label' => 'form_registration.submit',
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
