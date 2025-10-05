<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ActiveDirectorySyncOption;
use App\Form\ActiveDirectorySyncOptionType;
use App\Repository\ActiveDirectorySyncOptionRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActiveDirectorySyncOptionController extends AbstractController
{
    public function __construct(private readonly ActiveDirectorySyncOptionRepositoryInterface $repository)
    {
    }
    #[Route(path: '/admin/ad_sync', name: 'ad_sync_options')]
    public function index(): Response {
        $syncOptions = $this->repository->findAll();

        return $this->render('ad_sync_options/index.html.twig', [
            'sync_options' => $syncOptions
        ]);
    }

    #[Route(path: '/admin/ad_sync/add', name: 'add_ad_sync_option')]
    public function add(Request $request): Response {
        $syncOption = new ActiveDirectorySyncOption();

        $form = $this->createForm(ActiveDirectorySyncOptionType::class, $syncOption);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($syncOption);

            $this->addFlash('success', 'ad_sync_options.add.success');
            return $this->redirectToRoute('ad_sync_options');
        }

        return $this->render('ad_sync_options/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/admin/ad_sync/{uuid}/edit', name: 'edit_ad_sync_option')]
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] ActiveDirectorySyncOption $syncOption): Response {
        $form = $this->createForm(ActiveDirectorySyncOptionType::class, $syncOption);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($syncOption);

            $this->addFlash('success', 'ad_sync_options.edit.success');
            return $this->redirectToRoute('ad_sync_options');
        }

        return $this->render('ad_sync_options/edit.html.twig', [
            'form' => $form->createView(),
            'option' => $syncOption
        ]);
    }

    #[Route(path: '/admin/ad_sync/{uuid}/remove', name: 'remove_ad_sync_option')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] ActiveDirectorySyncOption $syncOption, Request $request, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, [], [
            'message' => $translator->trans('ad_sync_options.remove.confirm', [
                '%name%' => $syncOption->getName()
            ]),
            'label' => 'ad_sync_options.remove.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($syncOption);

            $this->addFlash('success', 'ad_sync_options.remove.success');
            return $this->redirectToRoute('ad_sync_options');
        }

        return $this->render('ad_sync_options/remove.html.twig', [
            'form' => $form->createView(),
            'option' => $syncOption
        ]);
    }
}
