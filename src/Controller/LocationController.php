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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for /location
 */
class LocationController extends AbstractController
{
    use CrudTrait;

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
     * @Route("/location/{id}", name="app_location", requirements={"id"="\d+"})
     */
    public function index(Request $request, int $id)
    {
        return $this->renderCustomForm([
            'entity'          => $this->getDoctrine()->getRepository(Location::class)->findOneById($id),
            'request'         => $request,
            'successCallback' => fn($entity) => 'Updated ' . $entity->getDisplayLabel(),
        ]);
    }

    /**
     * @Route("/location/new", name="app_location_new")
     */
    public function new(Request $request)
    {
        return $this->renderCustomForm([
            'entity'          => new Location(),
            'request'         => $request,
            'successCallback' => fn($entity) => 'Created ' . $entity->getDisplayLabel(),
        ]);
    }

    /**
     * @Route("/location/showAll", name="app_location_all")
     */
    public function showAll()
    {
        return $this->render(
            'location/all.html.twig',
            [
                'locations' => $this->getDoctrine()->getRepository(Location::class)->getSortedByDisplayLabel(),
            ]
        );
    }
}
