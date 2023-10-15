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
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for /box
 */
class BoxController extends AbstractController
{
    use CrudTrait;

    /**
     * Session index for controlling new box behavior.
     */
    protected const SESSION_NEW_BOX = 'nübox';

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
        'formClass'    => BoxType::class,
        'successRoute' => 'app_box_recent',
        'template'     => 'box/index.html.twig',
    ];

    /**
     * Constructs a new Box controller
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/box/{id}", name="app_box", requirements={"id"="\d+"})
     */
    public function index(Request $request, int $id)
    {
        return $this->renderCustomForm($this->em, [
            'entity'          => $this->em->getRepository(Box::class)->findOneById($id),
            'request'         => $request,
            'successCallback' => fn($entity) => 'Updated ' . $entity->getDisplayLabel(),
        ]);
    }

    /**
     * @Route("/box/new", name="app_box_new")
     */
    public function new(Request $request)
    {
        $session = $request->getSession();

        $session->set(static::SESSION_NEW_BOX, false);

        return $this->renderCustomForm($this->em, [
            'entity'          => new Box(),
            'request'         => $request,
            'successCallback' => fn() => 'Box created successfully.',
            'successRoute'    => null,
            'successRouteCallback' => function ($entity) use ($session) {
                $session->set(static::SESSION_NEW_BOX, true);
                return $this->redirectToRoute('app_box_search_id', ['id' => $entity->getId()]);
            },
        ]);
    }

    /**
     * @Route("/box/search", name="app_box_search")
     */
    public function search(Request $request): Response
    {
        $query = $request->get('q');
        $boxes = $this->em->getRepository(Box::class)->findByKeyword($query);
        return $this->render(
            'box/search.html.twig',
            [
                'boxes'  => $boxes,
                'query'  => $query,
                'type'   => null,
            ]
        );
    }

    /**
     * @Route("/box/search/id/{id}", name="app_box_search_id", requirements={"id"="\d+"})
     */
    public function searchById(Request $request, int $id)
    {
        $session = $request->getSession();

        $entity = $this->em->getRepository(Box::class)->find($id);
        if (!is_object($entity)) {
            $this->addFlash('error', 'Unable to show box ' . $id);
            return $this->redirectToRoute('app_home');
        }

        $hideMessage = $session->get(static::SESSION_NEW_BOX, false);
        $session->remove(static::SESSION_NEW_BOX);

        return $this->render(
            'box/search.html.twig',
            [
                'boxes'       => [$entity],
                'entity'      => $entity,
                'type'        => 'Box',
                'hideMessage' => $hideMessage,
            ]
        );
    }

    /**
     * @Route("/box/search/location/{id}", name="app_box_search_location", requirements={"id"="\d+"})
     */
    public function searchByLocation(int $id)
    {
        $entity = $this->em->getRepository(Location::class)->find($id);
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
        $entity = $this->em->getRepository(BoxModel::class)->find($id);
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
    public function showAll(): Response
    {
        return $this->render(
            'box/all.html.twig',
            [
                'boxes' => $this->em->getRepository(Box::class)->getSortedByDisplayLabel(),
                'title' => 'All Boxes',
            ]
        );
    }


    /**
     * @Route("/box/recent", name="app_box_recent")
     *
     * @throws Exception
     */
    public function showRecent(): Response
    {
        return $this->render(
            'box/all.html.twig',
            [
                'boxes' => $this->em->getRepository(Box::class)->getRecent('-30 days'),
                'title' => 'Boxes Added or Changed Over the Past 30 Days',
            ]
        );
    }
}
