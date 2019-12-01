<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Box;
use App\Entity\Location;

class LocationController extends AbstractController
{
    /**
     * @Route("/location/{id}", name="Location Page", requirements={"id"="\d+"})
     */
    public function index(int $id)
    {
        $repo = $this->getDoctrine()->getRepository(Location::class);
        $location = $repo->findOneById($id);
        return $this->render('location/index.html.twig', [
            'currentLocation' => $location,
            'parentLocation'  => $location->getParentLocation(),
            'locations'       => $repo->getSubLocations($id),
            'boxes'           => $location->getBoxes(),
        ]);
    }
}
