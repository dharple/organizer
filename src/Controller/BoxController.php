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
use App\Form\BoxType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for /box
 */
class BoxController extends AbstractController
{

    /**
     * @Route("/box/{id}", name="app_box", requirements={"id"="\d+"})
     */
    public function index(Request $request, int $id)
    {
        $box = $this->getDoctrine()->getRepository(Box::class)->findOneById($id);
        $form = $this->createForm(BoxType::class, $box);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $box = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($box);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render(
            'box/index.html.twig',
            [
                'box'  => $box,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/box/new", name="app_box_new")
     */
    public function new(Request $request)
    {
        $box = new Box();
        $form = $this->createForm(BoxType::class, $box);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $box = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($box);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render(
            'box/index.html.twig',
            [
                'box'  => $box,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/box/showAll", name="app_box_all")
     */
    public function showAll()
    {
        return $this->render(
            'box/all.html.twig',
            [
                'boxes' => $this->getDoctrine()->getRepository(Box::class)->findAll(),
            ]
        );
    }
}
