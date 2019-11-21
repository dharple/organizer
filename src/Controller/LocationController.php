<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Location;

class LocationController extends AbstractController
{
    /**
     * @Route("/location", name="location")
     */
    public function index()
    {
        return $this->render('location/index.html.twig', [
            'locations' => $this->getDoctrine()->getRepository(Location::class)->getTopLevelLocations(),
        ]);
    }

    /**
     * @Route("/location/{id}", name="sublocation", requirements={"id"="\d+"})
     */
    public function sublocation(int $id)
    {
        $repo = $this->getDoctrine()->getRepository(Location::class);
        return $this->render('location/index.html.twig', [
            'currentLocation' => $repo->findOneById($id),
            'locations' => $repo->getSubLocations($id),
        ]);
    }
}
