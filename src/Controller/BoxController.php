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
use App\Form\BoxType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for /box
 */
class BoxController extends AbstractController
{
    use CrudTrait;

    /**
     * Options that renderForm uses
     *
     * @var array
     */
    protected $formOptions = [
        'formClass'    => BoxType::class,
        'successRoute' => 'app_box_all',
        'template'     => 'box/index.html.twig',
    ];

    /**
     * @Route("/box/{id}", name="app_box", requirements={"id"="\d+"})
     */
    public function index(Request $request, int $id)
    {
        return $this->renderForm([
            'entity'          => $this->getDoctrine()->getRepository(Box::class)->findOneById($id),
            'request'         => $request,
            'successCallback' => function ($entity) {
                return 'Updated ' . $entity->getDisplayLabel();
            },
        ]);
    }

    /**
     * @Route("/box/new", name="app_box_new")
     */
    public function new(Request $request)
    {
        return $this->renderForm([
            'entity'          => new Box(),
            'request'         => $request,
            'successCallback' => function ($entity) {
                return 'Created ' . $entity->getDisplayLabel();
            },
        ]);
    }

    /**
     * @Route("/box/search", name="app_box_search")
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $boxes = $this->getDoctrine()->getRepository(Box::class)->findByKeyword($query);
        return $this->render(
            'box/search.html.twig',
            [
                'boxes'  => $boxes,
                'query'  => $query,
            ]
        );
    }

    /**
     * @Route("/box/search/location/{id}", name="app_box_search_location", requirements={"id"="\d+"})
     */
    public function searchByLocation(int $id)
    {
        $entity = $this->getDoctrine()->getRepository(Location::class)->find($id);
        if (!is_object($entity)) {
            $this->addFlash('error', 'Unable to show boxes for invalid location ' . $id);
            return $this->redirectToRoute('app_home');
        }

        return $this->render(
            'box/search.html.twig',
            [
                'boxes'  => $entity->getBoxes(),
                'entity' => $entity,
                'type'   => 'Location',
            ]
        );
    }

    /**
     * @Route("/box/search/model/{id}", name="app_box_search_model", requirements={"id"="\d+"})
     */
    public function searchByModel(int $id)
    {
        $entity = $this->getDoctrine()->getRepository(BoxModel::class)->find($id);
        if (!is_object($entity)) {
            $this->addFlash('error', 'Unable to show boxes for invalid box model ' . $id);
            return $this->redirectToRoute('app_home');
        }

        return $this->render(
            'box/search.html.twig',
            [
                'boxes'  => $entity->getBoxes(),
                'entity' => $entity,
                'type'   => 'BoxModel',
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
                'boxes' => $this->getDoctrine()->getRepository(Box::class)->getSorted(),
            ]
        );
    }
}
