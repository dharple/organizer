<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for /location
 */
class LocationController extends AbstractController
{
    use CrudTrait;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * Options that renderCustomForm uses
     *
     * @var array
     */
    protected $formOptions = [
        'formClass'    => LocationType::class,
        'successRoute' => 'app_location_all',
        'template'     => 'location/index.html.twig',
    ];

    /**
     * Constructs a new Location controller
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/location/{id}", name="app_location", requirements={"id"="\d+"})
     */
    public function index(Request $request, int $id)
    {
        return $this->renderCustomForm($this->em, [
            'entity'          => $this->em->getRepository(Location::class)->findOneById($id),
            'request'         => $request,
            'successCallback' => fn($entity) => 'Updated ' . $entity->getDisplayLabel(),
        ]);
    }

    /**
     * @Route("/location/new", name="app_location_new")
     */
    public function new(Request $request)
    {
        return $this->renderCustomForm($this->em, [
            'entity'          => new Location(),
            'request'         => $request,
            'successCallback' => fn($entity) => 'Created ' . $entity->getDisplayLabel(),
        ]);
    }

    /**
     * @Route("/location/showAll", name="app_location_all")
     */
    public function showAll(): Response
    {
        return $this->render(
            'location/all.html.twig',
            [
                'locations' => $this->em->getRepository(Location::class)->getSortedByDisplayLabel(),
            ]
        );
    }
}
