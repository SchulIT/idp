<?php

namespace App\Controller;

use App\Entity\KioskUser;
use App\Form\KioskUserType;
use App\Repository\KioskUserRepositoryInterface;
use App\Service\KioskUserTokenGenerator;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users/kiosk")
 */
class KioskUserController extends AbstractController {

    private $repository;

    public function __construct(KioskUserRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="kiosk_users")
     */
    public function index() {
        return $this->render('users/kiosk/index.html.twig', [
            'users' => $this->repository->findAll()
        ]);
    }

    /**
     * @Route("/add", name="add_kiosk_user")
     */
    public function add(Request $request, KioskUserTokenGenerator $tokenGenerator) {
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

    /**
     * @Route("/{uuid}/edit", name="edit_kiosk_user")
     */
    public function edit(KioskUser $user, Request $request) {
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

    /**
     * @Route("/{uuid}/remove", name="remove_kiosk_user")
     */
    public function remove(KioskUser $user, Request $request) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'users.kiosk.remove.confirm',
            'message_parameters' => [
                '%username%' => $user->getUser()->getUsername()
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