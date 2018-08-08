<?php

namespace App\Controller;

use App\Entity\UserType;
use App\Form\AttributeDataTrait;
use App\Form\UserTypeType;
use App\Repository\UserTypeRepositoryInterface;
use App\Service\AttributePersister;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

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

            $this->addFlash('success', 'user_types.add.success');
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

            $this->addFlash('success', 'user_types.edit.success');
            return $this->redirectToRoute('user_types');
        }

        return $this->render('user_types/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/remove", name="remove_user_type")
     */
    public function remove(UserType $type, Request $request, UserTypeRepositoryInterface $userTypeRepository, TranslatorInterface $translator) {
        if($userTypeRepository->countUsersOfUserType($type) > 0) {
            $this->addFlash('error', $translator->trans('user_types.remove.error', [
                '%name%' => $type->getName()
            ]));

            return $this->redirectToRoute('user_types');
        }

        $form = $this->createForm(ConfirmType::class, [], [
            'message' => $translator->trans('user_types.remove.confirm', [ '%name%' => $type->getName() ]),
            //'help' => $translator->trans('user_types.remove.help'),
            'header' => 'user_types.remove.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($type);
            $em->flush();

            $this->addFlash('success', 'user_types.remove.success');
            return $this->redirectToRoute('user_types');
        }

        return $this->render('user_types/remove.html.twig', [
            'form' => $form->createView(),
            'type' => $type
        ]);
    }
}