<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Box;
use App\Entity\Location;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="Home Page")
     */
    public function index()
    {
        return $this->render('index.html.twig', [
            'locations' => $this->getDoctrine()->getRepository(Location::class)->getTopLevelLocations(),
        ]);
    }

    /**
     * @Route("/allBoxes", name="All Boxes")
     */
    public function allBoxes()
    {
        return $this->render('allBoxes.html.twig', [
            'boxes' => $this->getDoctrine()->getRepository(Box::class)->findAll(),
        ]);
    }
}
