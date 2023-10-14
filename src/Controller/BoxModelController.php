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
    use CrudTrait;

    /**
     * Options that renderCustomForm uses
     *
     * @var array
     */
    protected $formOptions = [
        'formClass'    => BoxModelType::class,
        'successRoute' => 'app_box_model_all',
        'template'     => 'box_model/index.html.twig',
    ];

    /**
     * @Route("/box/model/{id}", name="app_box_model", requirements={"id"="\d+"})
     */
    public function index(Request $request, int $id)
    {
        return $this->renderCustomForm([
            'entity'          => $this->getDoctrine()->getRepository(BoxModel::class)->findOneById($id),
            'request'         => $request,
            'successCallback' => fn($entity) => 'Updated ' . $entity->getLabel(),
        ]);
    }

    /**
     * @Route("/box/model/new", name="app_box_model_new")
     */
    public function new(Request $request)
    {
        return $this->renderCustomForm([
            'entity'          => new BoxModel(),
            'request'         => $request,
            'successCallback' => fn($entity) => 'Created ' . $entity->getLabel(),
        ]);
    }

    /**
     * @Route("/box/model/showAll", name="app_box_model_all")
     */
    public function showAll()
    {
        return $this->render(
            'box_model/all.html.twig',
            [
                'boxModels' => $this->getDoctrine()->getRepository(BoxModel::class)->getSortedByDisplayLabel(),
            ]
        );
    }
}
