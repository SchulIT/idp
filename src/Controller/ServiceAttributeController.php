<?php

namespace App\Controller;

use App\Entity\ServiceAttribute;
use App\Form\ServiceAttributeType;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class ServiceAttributeController extends Controller {

    /**
     * @Route("/admin/attributes", name="attributes")
     */
    public function index() {
        $attributes = $this->getDoctrine()->getManager()
            ->getRepository(ServiceAttribute::class)
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($attribute);
            $em->flush();

            $this->addFlash('success', 'service_attributes.add.success');
            return $this->redirectToRoute('attributes');
        }

        return $this->render('service_attributes/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/attributes/{id}/edit", name="edit_attribute")
     */
    public function edit(Request $request, ServiceAttribute $attribute) {
        $form = $this->createForm(ServiceAttributeType::class, $attribute);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($attribute);
            $em->flush();

            $this->addFlash('success', 'service_attributes.edit.success');
            return $this->redirectToRoute('attributes');
        }

        return $this->render('service_attributes/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/attributes/{id}/remove", name="remove_attribute")
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
            $em = $this->getDoctrine()->getManager();
            $em->remove($attribute);
            $em->flush();

            $this->addFlash('success', 'service_attributes.remove.success');
            return $this->redirectToRoute('attributes');
        }

        return $this->render('service_attributes/remove.html.twig', [
            'form' => $form->createView(),
            'attribute' => $attribute
        ]);
    }


}