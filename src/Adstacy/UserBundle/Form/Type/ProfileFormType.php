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
            'label' => 'Profile Picture',
            'required' => false,
            'image_size' => 'small'
        ));
        parent::buildUserForm($builder, $options);
        $builder->add(
            $builder->create('interests', 'text', array(
                'label' => 'Interests',
                'required' => false,
                'attr' => array(
                    'placeholder' => '#fashion #men #medan'
                )
            ))->addModelTransformer(new HashtagToArrayTransformer())
        );
        $builder->add('realName', 'text', array(
            'label' => 'Name',
            'required' => false,
            'attr' => array(
                'placeholder' => 'Adstacy'
            )
        ));
        $builder->add('about', 'text', array(
            'label' => 'About Me',
            'required' => false,
            'attr' => array(
                'placeholder' => 'A men fashion enthusiasts'
            )
        ));
        $builder->add('save', 'submit', array(
            'label' => 'Save'
        ));
    }

    public function getName()
    {
        return 'adstacy_profile';
    }
}
