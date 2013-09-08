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
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'form.email',
                'translation_domain' => 'FOSUserBundle',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'email@example.com'
                ),
                'error_bubbling' => true
            ))
            ->add('username', 'text', array(
                'label' => 'form.username',
                'translation_domain' => 'FOSUserBundle',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Username'
                ),
                'error_bubbling' => true
            ))
            ->add('realName', 'text', array(
                'label' => 'form_registration.fullname',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Fullname'
                ),
                'error_bubbling' => true
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
                'error_bubbling' => true,
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('register', 'submit', array(
                'label' => 'form_registration.submit',
            ))
        ;
    }

    public function getName()
    {
        return 'adstacy_registration';
    }
}
