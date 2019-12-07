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

use App\Entity\BoxModel;
use App\Form\BoxModelType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for /box/model
 */
class BoxModelController extends AbstractController
{

    /**
     * @Route("/box/model/{id}", name="app_box_model", requirements={"id"="\d+"})
     */
    public function index(Request $request, int $id)
    {
        $boxModel = $this->getDoctrine()->getRepository(BoxModel::class)->findOneById($id);
        $form = $this->createForm(BoxModelType::class, $boxModel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $boxModel = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($boxModel);
            $em->flush();

            $this->addFlash('success', 'Updated ' . $boxModel->getLabel());
            return $this->redirectToRoute('app_box_model_all');
        }

        return $this->render(
            'box_model/index.html.twig',
            [
                'boxModel' => $boxModel,
                'form'     => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/box/model/new", name="app_box_model_new")
     */
    public function new(Request $request)
    {
        $boxModel = new BoxModel();
        $form = $this->createForm(BoxModelType::class, $boxModel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $boxModel = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($boxModel);
            $em->flush();

            $this->addFlash('success', 'Created ' . $boxModel->getLabel());
            return $this->redirectToRoute('app_box_model_all');
        }

        return $this->render(
            'box_model/index.html.twig',
            [
                'boxModel' => $boxModel,
                'form'     => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/box/model/showAll", name="app_box_model_all")
     */
    public function showAll()
    {
        return $this->render(
            'box_model/all.html.twig',
            [
                'boxModels' => $this->getDoctrine()->getRepository(BoxModel::class)->getSorted(),
            ]
        );
    }
}
