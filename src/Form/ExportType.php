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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form for choosing data export options.
 */
class ExportType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'format',
                ChoiceType::class,
                [
                    'label'    => 'File Format',
                    'choices'  => [
                        'CSV'   => 'csv',
                        'Excel' => 'xlsx',
                        'JSON'  => 'json',
                        'OpenDocument Spreadsheet' => 'ods',
                        'XML'   => 'xml',
                        'YAML'  => 'yaml',
                    ],
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label'    => 'Export Type',
                    'choices'  => [
                        'Simple Box Export (box card contents only)' => 'simple',
                        'Full Export (only supports JSON or XML)'    => 'full',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                ]
            )
            ->add('export', SubmitType::class);
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data' => [
                'format' => 'xlsx',
                'type'   => 'simple',
            ],
        ]);
    }
}
