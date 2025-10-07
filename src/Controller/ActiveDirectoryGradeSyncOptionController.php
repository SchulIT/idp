<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ActiveDirectoryGradeSyncOption;
use App\Form\ActiveDirectoryGradeSyncOptionType;
use App\Repository\ActiveDirectoryGradeSyncOptionRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActiveDirectoryGradeSyncOptionController extends AbstractController
{
    public function __construct(private readonly ActiveDirectoryGradeSyncOptionRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/ad_sync/grades', name: 'ad_grades_sync_options')]
    public function index(): Response {
        $options = $this->repository->findAll();

        return $this->render('ad_sync_options/grades/index.html.twig', [
            'sync_options' => $options
        ]);
    }

    #[Route(path: '/admin/ad_sync/grades/add', name: 'add_ad_grades_sync_options')]
    public function add(Request $request): Response {
        $option = new ActiveDirectoryGradeSyncOption();

        $form = $this->createForm(ActiveDirectoryGradeSyncOptionType::class, $option);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($option);

            $this->addFlash('success', 'ad_sync_options.grades.add.success');
            return $this->redirectToRoute('ad_grades_sync_options');
        }

        return $this->render('ad_sync_options/grades/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[NotFoundRedirect(redirectRoute: 'ad_grades_sync_options', flashMessage: 'ad_sync_options.not_found')]
    #[Route(path: '/admin/ad_sync/grades/{uuid}/edit', name: 'edit_ad_grades_sync_options')]
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] ActiveDirectoryGradeSyncOption $option): Response {
        $form = $this->createForm(ActiveDirectoryGradeSyncOptionType::class, $option);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($option);

            $this->addFlash('success', 'ad_sync_options.grades.add.success');
            return $this->redirectToRoute('ad_grades_sync_options');
        }

        return $this->render('ad_sync_options/grades/edit.html.twig', [
            'form' => $form->createView(),
            'option' => $option
        ]);
    }

    #[NotFoundRedirect(redirectRoute: 'ad_grades_sync_options', flashMessage: 'ad_sync_options.not_found')]
    #[Route(path: '/admin/ad_sync/grades/{uuid}/remove', name: 'remove_ad_grades_sync_options')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] ActiveDirectoryGradeSyncOption $option, Request $request, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, [], [
            'message' => $translator->trans('ad_sync_options.grades.remove.confirm', [
                '%grade%' => $option->getGrade()
            ]),
            'label' => 'ad_sync_options.grades.remove.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($option);

            $this->addFlash('success', 'ad_sync_options.grades.remove.success');
            return $this->redirectToRoute('ad_grades_sync_options');
        }

        return $this->render('ad_sync_options/grades/remove.html.twig', [
            'form' => $form->createView(),
            'option' => $option
        ]);
    }
}
