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

use App\Entity\Box;
use App\Entity\BoxModel;
use App\Entity\Location;
use App\Repository\BoxModelRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Builds a form for editing a box
 */
class BoxType extends AbstractType
{
    /**
     * @var BoxModelRepository
     */
    protected $boxModelRepository;

    /**
     * @var LocationRepository
     */
    protected $locationRepository;

    /**
     * Constructor
     */
    public function __construct(
        BoxModelRepository $boxModelRepository,
        LocationRepository $locationRepository
    ) {
        $this->boxModelRepository = $boxModelRepository;
        $this->locationRepository = $locationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label')
            ->add('description')
            ->add(
                'location',
                EntityType::class,
                [
                    'choice_label' => 'displayLabel',
                    'class'        => Location::class,
                    'choices'      => $this->locationRepository->getSorted(),
                    'placeholder'  => 'Choose a location...',
                    'required'     => false,
                ]
            )
            ->add(
                'boxModel',
                EntityType::class,
                [
                    'choice_label' => 'label',
                    'class'        => BoxModel::class,
                    'choices'      => $this->boxModelRepository->getSorted(),
                    'placeholder'  => 'Choose a model...',
                    'required'     => false,
                ]
            )
            ->add('save', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Box::class,
        ]);
    }
}
