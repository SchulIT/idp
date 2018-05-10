<?php

namespace App\Controller;

use App\Entity\ServiceAttribute;
use App\Form\ServiceAttributeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

            return $this->redirectToRoute('attributes');
        }

        return $this->render('service_attributes/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/attributes/{id}/remove", name="remove_attribute")
     */
    public function remove() {

    }


}