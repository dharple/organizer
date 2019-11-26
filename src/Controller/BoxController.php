<?php

namespace App\Controller;

use App\Entity\Box;
use App\Form\BoxType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BoxController extends AbstractController
{
    /**
     * @Route("/box/{id}", name="box", requirements={"id"="\d+"})
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
		}

        return $this->render('box/index.html.twig', [
            'currentBox' => $box,
            'location'   => $box->getLocation(),
            'form'       => $form->createView(),
        ]);
    }

}
