<?php

namespace App\Controller;

use App\Entity\UserRole;
use App\Form\AttributeDataTrait;
use App\Form\UserRoleType;
use App\Service\AttributePersister;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/admin/user_roles")
 */
class UserRoleController extends Controller {

    use AttributeDataTrait;

    /**
     * @Route("", name="user_roles")
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
     * @Route("/add", name="add_role")
     */
    public function add(Request $request, AttributePersister $attributePersister) {
        $role = new UserRole();

        $form = $this->createForm(UserRoleType::class, $role);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserRoleAttributes($attributeData, $role);

            $this->addFlash('success', 'user_roles.add.success');
            return $this->redirectToRoute('user_roles');
        }

        return $this->render('user_roles/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_role")
     */
    public function edit(Request $request, UserRole $role, AttributePersister $attributePersister) {
        $form = $this->createForm(UserRoleType::class, $role);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserRoleAttributes($attributeData, $role);

            $this->addFlash('success', 'user_roles.edit.success');
            return $this->redirectToRoute('user_roles');
        }

        return $this->render('user_roles/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/remove", name="remove_role")
     */
    public function remove(UserRole $role, Request $request, TranslatorInterface $translator) {
        $form = $this->createForm(ConfirmType::class, [ ], [
            'message' => $translator->trans('user_roles.remove.confirm', [
                '%name%' => $role->getName()
            ]),
            'label' => 'user_roles.remove.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($role);
            $em->flush();

            $this->addFlash('success', 'user_roles.remove.success');
            return $this->redirectToRoute('user_roles');
        }

        return $this->render('user_roles/remove.html.twig', [
            'form' => $form->createView(),
            'role' => $role
        ]);
    }
}