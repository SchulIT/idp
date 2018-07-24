<?php

namespace App\Controller;

use App\Entity\UserType;
use App\Form\UserTypeType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserTypeController extends Controller {

    /**
     * @Route("/admin/user_types", name="user_types")
     */
    public function index() {
        $userTypes = $this->getDoctrine()->getManager()
            ->getRepository(UserType::class)
            ->findAll();

        return $this->render('user_types/index.html.twig', [
            'user_types' => $userTypes
        ]);
    }

    /**
     * @Route("/admin/user_types/add", name="add_user_type")
     */
    public function add(Request $request) {
        $userType = new UserType();

        $form = $this->createForm(UserTypeType::class, $userType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userType);
            $em->flush();

            $userTypeType = $this->get('forms.user_type_type');
            $attributeData = $userTypeType->getAttributeData($form);

            $attributePersister = $this->get('attribute.persister');
            $attributePersister->persistUserTypeAttributes($attributeData, $userType);

            return $this->redirectToRoute('user_types');
        }

        return $this->render('user_types/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user_types/{id}/edit", name="edit_user_type")
     */
    public function edit(Request $request, UserType $userType) {
        $form = $this->createForm(UserTypeType::class, $userType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userType);
            $em->flush();

            $userTypeType = $this->get('forms.user_type_type');
            $attributeData = $userTypeType->getAttributeData($form);

            $attributePersister = $this->get('attribute.persister');
            $attributePersister->persistUserTypeAttributes($attributeData, $userType);

            return $this->redirectToRoute('user_types');
        }

        return $this->render('user_types/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user_types/{id}/remove", name="remove_user_type")
     */
    public function remove() {

    }
}