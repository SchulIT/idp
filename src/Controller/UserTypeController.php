<?php

namespace App\Controller;

use App\Entity\UserType;
use App\Form\AttributeDataTrait;
use App\Form\UserTypeType;
use App\Service\AttributePersister;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/user_types")
 */
class UserTypeController extends Controller {

    use AttributeDataTrait;

    /**
     * @Route("", name="user_types")
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
     * @Route("/add", name="add_user_type")
     */
    public function add(Request $request, AttributePersister $attributePersister) {
        $userType = new UserType();

        $form = $this->createForm(UserTypeType::class, $userType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userType);
            $em->flush();

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserTypeAttributes($attributeData, $userType);

            return $this->redirectToRoute('user_types');
        }

        return $this->render('user_types/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_user_type")
     */
    public function edit(Request $request, UserType $userType, AttributePersister $attributePersister) {
        $form = $this->createForm(UserTypeType::class, $userType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userType);
            $em->flush();

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserTypeAttributes($attributeData, $userType);

            return $this->redirectToRoute('user_types');
        }

        return $this->render('user_types/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/remove", name="remove_user_type")
     */
    public function remove() {

    }
}