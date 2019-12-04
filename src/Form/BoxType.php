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
     * @var LocationRepository
     */
    protected $lr;

    /**
     * Constructor
     */
    public function __construct(LocationRepository $lr)
    {
        $this->lr = $lr;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
                    'choices'      => $this->lr->getSortedLocations(),
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
                    'placeholder'  => 'Choose a model...',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('m')
                            ->orderBy('m.label', 'ASC');
                    },
                    'required'     => false,
                ]
            )
            ->add('save', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Box::class,
        ]);
    }
}
