<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Box;
use App\Entity\Location;

class LocationController extends AbstractController
{
    /**
     * @Route("/", name="location")
     */
    public function index()
    {
        return $this->render('location/index.html.twig', [
            'locations' => $this->getDoctrine()->getRepository(Location::class)->getTopLevelLocations(),
            'boxes' => [],
        ]);
    }

    /**
     * @Route("/location/{id}", name="sublocation", requirements={"id"="\d+"})
     */
    public function sublocation(int $id)
    {
        $repo = $this->getDoctrine()->getRepository(Location::class);
        $location = $repo->findOneById($id);
        return $this->render('location/sublocation.html.twig', [
            'currentLocation' => $location,
            'parentLocation' => $location->getParentLocation(),
            'locations' => $repo->getSubLocations($id),
            'boxes' => $this->getDoctrine()->getRepository(Box::class)->getByLocation($id),
        ]);
    }
}
