<?php

namespace App\Controller;

use App\Entity\ServiceAttribute;
use App\Form\ServiceAttributeType;
use App\Repository\ServiceAttributeRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ServiceAttributeController extends AbstractController {

    public function __construct(private readonly ServiceAttributeRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/attributes', name: 'attributes')]
    public function index(): Response {
        $attributes = $this->repository
            ->findAll();

        return $this->render('service_attributes/index.html.twig', [
            'attributes' => $attributes
        ]);
    }

    #[Route(path: '/admin/attributes/add', name: 'add_attribute')]
    public function add(Request $request): Response {
        $attribute = new ServiceAttribute();

        $form = $this->createForm(ServiceAttributeType::class, $attribute);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($attribute);

            $this->addFlash('success', 'service_attributes.add.success');
            return $this->redirectToRoute('attributes');
        }

        return $this->render('service_attributes/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/admin/attributes/{uuid}/edit', name: 'edit_attribute')]
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] ServiceAttribute $attribute): Response {
        $form = $this->createForm(ServiceAttributeType::class, $attribute);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($attribute);

            $this->addFlash('success', 'service_attributes.edit.success');
            return $this->redirectToRoute('attributes');
        }

        return $this->render('service_attributes/edit.html.twig', [
            'form' => $form->createView(),
            'attribute' => $attribute
        ]);
    }

    #[Route(path: '/admin/attributes/{uuid}/remove', name: 'remove_attribute')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] ServiceAttribute $attribute, Request $request, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, [], [
            'message' => $translator->trans('service_attributes.remove.confirm', [
                '%name%' => $attribute->getName()
            ]),
            'label' => 'service_attributes.remove.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($attribute);

            $this->addFlash('success', 'service_attributes.remove.success');
            return $this->redirectToRoute('attributes');
        }

        return $this->render('service_attributes/remove.html.twig', [
            'form' => $form->createView(),
            'attribute' => $attribute
        ]);
    }


}