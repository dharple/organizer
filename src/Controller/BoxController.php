<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Box;

class BoxController extends AbstractController
{
    /**
     * @Route("/box/{id}", name="box", requirements={"id"="\d+"}, methods={"GET", "HEAD"})
     */
    public function index(int $id)
    {
        return $this->render('box/index.html.twig', [
            'currentBox' => $this->getDoctrine()->getRepository(Box::class)->findOneById($id),
        ]);
    }

    /**
     * @Route("/box/{id}", name="box editor", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function edit(int $id)
    {
        $box = $this->getDoctrine()->getRepository(Box::class)->findOneById($id);
        // ...
        return $this->forward('\App\Controller\BoxController::index', [
            'id' => $id,
        ]);
    }
}
