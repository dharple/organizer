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

use App\Entity\BoxModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Builds a form for a box model (type of container)
 */
class BoxModelType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $lpIgnore = ['attr' => ['data-lpignore' => 'true']];
        $builder
            ->add('label', null, $lpIgnore)
            ->add('make', null, $lpIgnore)
            ->add('model', null, $lpIgnore)
            ->add('size', null, $lpIgnore)
            ->add('color', null, $lpIgnore)
            ->add('latch', null, $lpIgnore)
            ->add('save', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BoxModel::class,
        ]);
    }
}
