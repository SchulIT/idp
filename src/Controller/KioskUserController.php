<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\KioskUser;
use App\Form\KioskUserType;
use App\Repository\KioskUserRepositoryInterface;
use App\Service\KioskUserTokenGenerator;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class KioskUserController extends AbstractController
{
    public function __construct(private readonly KioskUserRepositoryInterface $repository)
    {
    }
    #[Route(path: '/users/kiosk', name: 'kiosk_users')]
    public function index(): Response {
        return $this->render('users/kiosk/index.html.twig', [
            'users' => $this->repository->findAll()
        ]);
    }
    #[Route(path: '/users/kiosk/add', name: 'add_kiosk_user')]
    public function add(Request $request, KioskUserTokenGenerator $tokenGenerator): Response {
        $user = (new KioskUser())
            ->setToken($tokenGenerator->generateToken());
        $form = $this->createForm(KioskUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($user);

            $this->addFlash('success', 'users.kiosk.add.success');
            return $this->redirectToRoute('kiosk_users');
        }

        return $this->render('users/kiosk/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[NotFoundRedirect(redirectRoute: 'kiosk_users', flashMessage: 'users.not_found')]
    #[Route(path: '/users/kiosk/{uuid}/edit', name: 'edit_kiosk_user')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] KioskUser $user, Request $request): Response {
        $form = $this->createForm(KioskUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($user);

            $this->addFlash('success', 'users.kiosk.edit.success');
            return $this->redirectToRoute('kiosk_users');
        }

        return $this->render('users/kiosk/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[NotFoundRedirect(redirectRoute: 'kiosk_users', flashMessage: 'users.not_found')]
    #[Route(path: '/users/kiosk/{uuid}/remove', name: 'remove_kiosk_user')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] KioskUser $user, Request $request): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'users.kiosk.remove.confirm',
            'message_parameters' => [
                '%username%' => $user->getUser()->getUserIdentifier()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($user);

            $this->addFlash('success', 'users.kiosk.add.success');
            return $this->redirectToRoute('kiosk_users');
        }

        return $this->render('users/kiosk/remove.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
