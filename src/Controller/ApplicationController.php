<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Application;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepositoryInterface;
use App\Service\ApplicationKeyGenerator;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApplicationController extends AbstractController
{
    public function __construct(private readonly ApplicationRepositoryInterface $repository)
    {
    }
    #[Route(path: '/admin/applications', name: 'applications')]
    public function index(): Response {
        $applications = $this->repository->findAll();

        return $this->render('applications/index.html.twig', [
            'applications' => $applications
        ]);
    }
    #[Route(path: '/admin/applications/add', name: 'add_application')]
    public function add(Request $request, ApplicationKeyGenerator $keyGenerator): Response {
        $application = (new Application());
        $application->setApiKey($keyGenerator->generateApiKey());

        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($application);
            $this->addFlash('success', 'applications.add.success');

            return $this->redirectToRoute('applications');
        }

        return $this->render('applications/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[NotFoundRedirect(redirectRoute: 'applications', flashMessage: 'applications.not_found')]
    #[Route(path: '/admin/applications/{uuid}/edit', name: 'edit_application')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] Application $application, Request $request): Response {
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($application);
            $this->addFlash('success', 'applications.edit.success');

            return $this->redirectToRoute('applications');
        }

        return $this->render('applications/edit.html.twig', [
            'form' => $form->createView(),
            'application' => $application
        ]);
    }
    #[Route(path: '/admin/applications/{uuid}/remove', name: 'remove_application')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] Application $application, Request $request): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'applications.remove.confirm',
            'message_parameters' => [
                '%name%' => $application->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($application);
            $this->addFlash('success', 'applications.remove.success');

            return $this->redirectToRoute('applications');
        }

        return $this->render('applications/remove.html.twig', [
            'application' => $application,
            'form' => $form->createView()
        ]);
    }
}
