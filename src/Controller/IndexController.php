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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for /
 */
class IndexController extends AbstractController
{
    #[Route(path: '/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }

    #[Route(path: '/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render(
            'index.html.twig',
            [
                'boxCount'      => $em->getRepository(Box::class)->count([]),
                'boxModelCount' => $em->getRepository(BoxModel::class)->count([]),
                'locationCount' => $em->getRepository(Location::class)->count([]),
                'locations'     => $em->getRepository(Location::class)->getSortedByDisplayLabelWithBoxes(),
                'recentBoxes'   => $em->getRepository(Box::class)->getRecent('-1 week', 3),
            ]
        );
    }
}
