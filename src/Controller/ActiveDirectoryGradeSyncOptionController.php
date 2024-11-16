<?php

namespace App\Controller;

use App\Entity\ActiveDirectoryGradeSyncOption;
use App\Form\ActiveDirectoryGradeSyncOptionType;
use App\Repository\ActiveDirectoryGradeSyncOptionRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/ad_sync/grades')]
class ActiveDirectoryGradeSyncOptionController extends AbstractController {

    public function __construct(private readonly ActiveDirectoryGradeSyncOptionRepositoryInterface $repository)
    {
    }

    #[Route(path: '', name: 'ad_grades_sync_options')]
    public function index(): Response {
        $options = $this->repository->findAll();

        return $this->render('ad_sync_options/grades/index.html.twig', [
            'sync_options' => $options
        ]);
    }

    #[Route(path: '/add', name: 'add_ad_grades_sync_options')]
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

    #[Route(path: '/{uuid}/edit', name: 'edit_ad_grades_sync_options')]
    public function edit(Request $request, ActiveDirectoryGradeSyncOption $option): Response {
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

    #[Route(path: '/{uuid}/remove', name: 'remove_ad_grades_sync_options')]
    public function remove(ActiveDirectoryGradeSyncOption $option, Request $request, TranslatorInterface $translator): Response {
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