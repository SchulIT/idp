<?php

namespace App\Controller;

use App\Entity\ServiceAttribute;
use App\Form\ServiceAttributeType;
use App\Repository\ServiceAttributeRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ServiceAttributeController extends AbstractController {

    private $repository;

    public function __construct(ServiceAttributeRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("/admin/attributes", name="attributes")
     */
    public function index() {
        $attributes = $this->repository
            ->findAll();

        return $this->render('service_attributes/index.html.twig', [
            'attributes' => $attributes
        ]);
    }

    /**
     * @Route("/admin/attributes/add", name="add_attribute")
     */
    public function add(Request $request) {
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

    /**
     * @Route("/admin/attributes/{uuid}/edit", name="edit_attribute")
     */
    public function edit(Request $request, ServiceAttribute $attribute) {
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

    /**
     * @Route("/admin/attributes/{uuid}/remove", name="remove_attribute")
     */
    public function remove(ServiceAttribute $attribute, Request $request, TranslatorInterface $translator) {
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