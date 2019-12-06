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

    /**
     * @Route("/location/{id}", name="app_location", requirements={"id"="\d+"})
     */
    public function index(Request $request, int $id)
    {
        $location = $this->getDoctrine()->getRepository(Location::class)->findOneById($id);
        $form = $this->createForm(LocationType::class, $location);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $location = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render(
            'location/index.html.twig',
            [
                'location' => $location,
                'form'     => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/location/new", name="app_location_new")
     */
    public function new(Request $request)
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $location = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render(
            'location/index.html.twig',
            [
                'location' => $location,
                'form'     => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/location/showAll", name="app_location_all")
     */
    public function showAll()
    {
        return $this->render(
            'location/all.html.twig',
            [
                'locations' => $this->getDoctrine()->getRepository(Location::class)->getSorted(),
            ]
        );
    }
}
