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

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Controller for /profile
 */
class ProfileController extends AbstractController
{
    /**
     * Constructs a new Location controller
     */
    public function __construct(EntityManagerInterface $em)
    {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/profile', name: 'app_profile')]
    public function index(
        Request $request,
        TokenStorageInterface $tokenStorage,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $user = $tokenStorage->getToken()->getUser();
        if (!is_object($user)) {
            throw new Exception('Unable to load user profile');
        }

        if (!$user instanceof User) {
            throw new Exception(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        //
        // We're passing a clone of user into the form because the form uses a
        // Validator to check the current password and if it fails, the user
        // will get logged out.
        //
        $form = $this->createForm(UserType::class, clone $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Password Updated');
            return $this->redirectToRoute('app_home');
        }

        $response = $this->render(
            'profile/index.html.twig',
            [
                'user'    => $user,
                'form'    => $form->createView(),
            ]
        );

        if ($form->isSubmitted()) {
            // but not valid
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
