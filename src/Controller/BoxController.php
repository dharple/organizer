<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Box;

class BoxController extends AbstractController
{
    /**
     * @Route("/box/{id}", name="box", requirements={"id"="\d+"})
     */
    public function index(int $id)
    {
        return $this->render('box/index.html.twig', [
            'currentBox' => $this->getDoctrine()->getRepository(Box::class)->findOneById($id),
        ]);
    }
}
