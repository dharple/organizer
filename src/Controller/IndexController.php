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

use App\Entity\Box;
use App\Entity\Location;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for /
 */
class IndexController extends AbstractController
{

    /**
     * @Route("/allBoxes", name="All Boxes")
     */
    public function allBoxes()
    {
        return $this->render(
            'allBoxes.html.twig',
            [
                'boxes' => $this->getDoctrine()->getRepository(Box::class)->findAll(),
            ]
        );
    }

    /**
     * @Route("/", name="Home Page")
     */
    public function index()
    {
        return $this->render(
            'index.html.twig',
            [
                'locations' => $this->getDoctrine()->getRepository(Location::class)->getSortedLocationsWithBoxes(),
            ]
        );
    }
}
