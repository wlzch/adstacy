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
        parent::buildUserForm($builder, $options);
        $builder->add(
            $builder->create('interests', 'text', array(
                'label' => 'Interests',
                'required' => false 
            ))->addModelTransformer(new HashtagToArrayTransformer())
        );
        $builder->add('about', 'text', array(
            'label' => 'About Me',
            'required' => false
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
