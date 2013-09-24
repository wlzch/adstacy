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

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Adstacy\AppBundle\Form\DataTransformer\HashtagToArrayTransformer;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('image', 'image', array(
            'label' => 'form_profile.profile_picture',
            'required' => false,
            'image_required' => false,
            'image_size' => 'small',
            'error_bubbling' => true,
            'additional_fields' => array(
                'profilePicture'
            )
        ));
        $builder->add('username', null, array(
            'label' => 'form.username',
            'translation_domain' => 'FOSUserBundle',
            'error_bubbling' => true
        ));
        $builder->add('email', 'email', array(
            'label' => 'form.email',
            'translation_domain' => 'FOSUserBundle',
            'error_bubbling' => true
        ));
        $builder->add('realName', 'text', array(
            'label' => 'form_profile.fullname',
            'required' => 'required',
            'attr' => array(
                'placeholder' => 'Adstacy'
            ),
            'error_bubbling' => true
        ));
        $builder->add('about', 'purified_textarea', array(
            'label' => 'form_profile.about_me',
            'required' => false,
            'attr' => array(
                'placeholder' => 'form_profile.placeholders.about',
                'rows' => 5
            ),
            'error_bubbling' => true
        ));
        $builder->add('subscription', 'checkbox', array(
            'label' => 'form_profile.subscription',
            'required' => false,
            'error_bubbling' => true
        ));
        $builder->add('save', 'submit', array(
            'label' => 'form_profile.submit'
        ));
    }

    public function getName()
    {
        return 'adstacy_profile';
    }
}
