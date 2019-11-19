<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Location;

class OrganizerController extends AbstractController
{
    /**
     * @Route("/organizer", name="organizer")
     */
    public function index()
    {
        return $this->render('organizer/index.html.twig', [
            'locations' => $this->getDoctrine()->getRepository(Location::class)->getTopLevelLocations(),
        ]);
    }
}
