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

use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait for basic CRUD operations
 */
trait CrudTrait
{
    /**
     * All data is passed to the form via the options array
     *
     * [
     *  'entity'          => $box,          // the Entity to edit
     *  'formClass'       => BoxType::class // the form class to use
     *  'request'         => $request       // the Request passed in to the controller
     *  'successCallback' => callable       // what to call if the update
     *                                         succeeded.  Passes the entity as
     *                                         the first and only parameter.
     *                                         Must return a string.
     *  'successRoute'    => 'app_home'     // where to send the user after a successful update
     *  'successRouteCallback' => callable  // where to send the user after a successful update.
     *                                         Must return a Result object.
     *  'template'        => 'box/index.html.twig' // the template to use
     * ]
     */
    protected function renderCustomForm(EntityManagerInterface $em, array $options)
    {
        // @phpstan-ignore-next-line

        if (isset($this->formOptions)) {
            $options += $this->formOptions;
        }

        $entity = $options['entity'];
        $form = $this->createForm($options['formClass'], $entity);

        $form->handleRequest($options['request']);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity = $form->getData();

            $em->persist($entity);
            $em->flush();

            if (isset($options['successCallback'])) {
                $this->addFlash('success', call_user_func_array($options['successCallback'], [$entity]));
            }

            if (isset($options['successRoute'])) {
                return $this->redirectToRoute($options['successRoute']);
            }

            if (isset($options['successRouteCallback'])) {
                return call_user_func_array($options['successRouteCallback'], [$entity]);
            }
        }

        return $this->render(
            $options['template'],
            [
                'entity'  => $entity,
                'form'    => $form->createView(),
            ]
        );
    }
}
