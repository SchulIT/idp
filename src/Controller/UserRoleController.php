<?php

namespace App\Controller;

use App\Entity\UserRole;
use App\Form\UserRoleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserRoleController extends Controller {
    /**
     * @Route("/admin/user_roles", name="user_roles")
     */
    public function index() {
        $roles = $this->getDoctrine()->getManager()
            ->getRepository(UserRole::class)
            ->findAll();

        return $this->render('user_roles/index.html.twig', [
            'roles' => $roles
        ]);
    }

    /**
     * @Route("/admin/user_roles/add", name="add_role")
     */
    public function add(Request $request) {
        $role = new UserRole();

        $form = $this->createForm(UserRoleType::class, $role);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            $userRoleType = $this->get('forms.user_role_type');
            $attributeData = $userRoleType->getAttributeData($form);

            $attributePersister = $this->get('attribute.persister');
            $attributePersister->persistUserRoleAttributes($attributeData, $role);

            return $this->redirectToRoute('user_roles');
        }

        return $this->render('user_roles/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user_roles/{id}/edit", name="edit_role")
     */
    public function edit(Request $request, UserRole $role) {
        $form = $this->createForm(UserRoleType::class, $role);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            $userRoleType = $this->get('forms.user_role_type');
            $attributeData = $userRoleType->getAttributeData($form);

            $attributePersister = $this->get('attribute.persister');
            $attributePersister->persistUserRoleAttributes($attributeData, $role);

            return $this->redirectToRoute('user_roles');
        }

        return $this->render('user_roles/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user_roles/{id}/remove", name="remove_role")
     */
    public function remove() {

    }
}