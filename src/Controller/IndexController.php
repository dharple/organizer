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
use App\Entity\BoxModel;
use App\Entity\Location;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for /
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/about", name="app_about")
     */
    public function about()
    {
        return $this->render('about.html.twig');
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index()
    {
        $em = $this->getDoctrine();

        return $this->render(
            'index.html.twig',
            [
                'boxCount'      => $em->getRepository(Box::class)->count([]),
                'boxModelCount' => $em->getRepository(BoxModel::class)->count([]),
                'locationCount' => $em->getRepository(Location::class)->count([]),
                'locations'     => $em->getRepository(Location::class)->getSortedByDisplayLabelWithBoxes(),
            ]
        );
    }
}
