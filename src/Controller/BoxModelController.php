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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for /box/model
 */
class BoxModelController extends AbstractController
{
    use CrudTrait;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

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
     * Constructs a new Box Model controller
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/box/model/{id}", name="app_box_model", requirements={"id"="\d+"})
     */
    public function index(Request $request, int $id)
    {
        return $this->renderCustomForm($this->em, [
            'entity'          => $this->em->getRepository(BoxModel::class)->findOneById($id),
            'request'         => $request,
            'successCallback' => fn($entity) => 'Updated ' . $entity->getLabel(),
        ]);
    }

    /**
     * @Route("/box/model/new", name="app_box_model_new")
     */
    public function new(Request $request)
    {
        return $this->renderCustomForm($this->em, [
            'entity'          => new BoxModel(),
            'request'         => $request,
            'successCallback' => fn($entity) => 'Created ' . $entity->getLabel(),
        ]);
    }

    /**
     * @Route("/box/model/showAll", name="app_box_model_all")
     */
    public function showAll(): Response
    {
        return $this->render(
            'box_model/all.html.twig',
            [
                'boxModels' => $this->em->getRepository(BoxModel::class)->getSortedByDisplayLabel(),
            ]
        );
    }
}
