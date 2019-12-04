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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for /location
 */
class LocationController extends AbstractController
{

    /**
     * @Route("/location/{id}", name="Location Page", requirements={"id"="\d+"})
     */
    public function index(int $id)
    {
        $repo = $this->getDoctrine()->getRepository(Location::class);
        $location = $repo->findOneById($id);
        return $this->render(
            'location/index.html.twig',
            [
                'currentLocation' => $location,
                'parentLocation'  => $location->getParentLocation(),
                'locations'       => $repo->getSubLocations($id),
                'boxes'           => $location->getBoxes(),
            ]
        );
    }
}
