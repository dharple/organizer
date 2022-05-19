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

use App\Entity\Location;
use App\Repository\LocationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Builds a form for editing a location
 */
class LocationType extends AbstractType
{
    /**
     * @var LocationRepository
     */
    protected $locationRepository;

    /**
     * Constructor
     */
    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label')
            ->add(
                'parentLocation',
                EntityType::class,
                [
                    'choice_label' => 'displayLabel',
                    'class'        => Location::class,
                    'choices'      => $this->locationRepository->getSortedByDisplayLabel(),
                    'placeholder'  => 'This is a Top Level Location',
                    'required'     => false,
                ]
            )
            ->add('description')
            ->add('hideFromSearch')
            ->add('save', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
