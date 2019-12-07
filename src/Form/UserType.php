<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 * Form for updating a user's password
 */
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                TextType::class,
                ['disabled' => true]
            )
            ->add(
                'oldPassword',
                PasswordType::class,
                [
                    'label'           => 'Current Password',
                    'required'        => true,
                    'mapped'          => false,
                    'constraints'     => new UserPassword([
                        'message'     => 'Password does not match our records'
                    ]),
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type'            => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options'         => [
                        'attr'        => [
                            'class'   => 'password-field'
                        ]
                    ],
                    'required'        => true,
                    'first_options'   => ['label' => 'Password'],
                    'second_options'  => ['label' => 'Confirm Password'],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                ['label' => 'Update Password']
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
