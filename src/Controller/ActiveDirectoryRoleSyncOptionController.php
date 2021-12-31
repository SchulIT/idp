<?php

namespace App\Controller;

use App\Entity\ActiveDirectoryRoleSyncOption;
use App\Form\ActiveDirectoryRoleSyncOptionType;
use App\Repository\ActiveDirectoryRoleSyncOptionRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/ad_sync/roles")
 */
class ActiveDirectoryRoleSyncOptionController extends AbstractController {

    private ActiveDirectoryRoleSyncOptionRepositoryInterface $repository;

    public function __construct(ActiveDirectoryRoleSyncOptionRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="ad_roles_sync_options")
     */
    public function index(): Response {
        $options = $this->repository->findAll();

        return $this->render('ad_sync_options/roles/index.html.twig', [
            'sync_options' => $options
        ]);
    }

    /**
     * @Route("/add", name="add_ad_roles_sync_options")
     */
    public function add(Request $request): Response {
        $option = new ActiveDirectoryRoleSyncOption();

        $form = $this->createForm(ActiveDirectoryRoleSyncOptionType::class, $option);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($option);

            $this->addFlash('success', 'ad_sync_options.roles.add.success');
            return $this->redirectToRoute('ad_roles_sync_options');
        }

        return $this->render('ad_sync_options/roles/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_ad_role_sync_options")
     */
    public function edit(Request $request, ActiveDirectoryRoleSyncOption $option): Response {
        $form = $this->createForm(ActiveDirectoryRoleSyncOptionType::class, $option);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($option);

            $this->addFlash('success', 'ad_sync_options.roles.add.success');
            return $this->redirectToRoute('ad_roles_sync_options');
        }

        return $this->render('ad_sync_options/roles/edit.html.twig', [
            'form' => $form->createView(),
            'option' => $option
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_ad_role_sync_options")
     */
    public function remove(ActiveDirectoryRoleSyncOption $option, Request $request, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, [], [
            'message' => $translator->trans('ad_sync_options.roles.remove.confirm', [
                '%role%' => $option->getUserRole()->getName()
            ]),
            'label' => 'ad_sync_options.roles.remove.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($option);

            $this->addFlash('success', 'ad_sync_options.roles.remove.success');
            return $this->redirectToRoute('ad_roles_sync_options');
        }

        return $this->render('ad_sync_options/roles/remove.html.twig', [
            'form' => $form->createView(),
            'option' => $option
        ]);
    }
}