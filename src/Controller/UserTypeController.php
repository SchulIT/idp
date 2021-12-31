<?php

namespace App\Controller;

use App\Entity\UserType;
use App\Form\AttributeDataTrait;
use App\Form\UserTypeType;
use App\Repository\UserTypeRepositoryInterface;
use App\Security\Voter\UserTypeVoter;
use App\Service\AttributePersister;
use App\Setup\UserTypesSetup;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/user_types")
 */
class UserTypeController extends AbstractController {

    use AttributeDataTrait;

    private const CSRF_TOKEN_ID = 'setup.user_types';
    private const CSRF_TOKEN_KEY = '_csrf_token';

    private UserTypeRepositoryInterface $repository;

    public function __construct(UserTypeRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="user_types")
     */
    public function index(UserTypesSetup $setup): Response {
        $userTypes = $this->repository
            ->findAll();

        return $this->render('user_types/index.html.twig', [
            'user_types' => $userTypes,
            'csrf_token_id'=> static::CSRF_TOKEN_ID,
            'csrf_token_key' => static::CSRF_TOKEN_KEY,
            'can_setup' => $setup->canSetup()
        ]);
    }

    /**
     * @Route("/setup", name="setup_user_types", methods={"POST"})
     */
    public function setupDefaultUserTypes(Request $request, UserTypesSetup $setup, TranslatorInterface $translator): Response {
        if($this->isCsrfTokenValid(static::CSRF_TOKEN_ID, $request->request->get(static::CSRF_TOKEN_KEY)) !== true) {
            $this->addFlash('error', $translator->trans('Invalid CSRF token.', [], 'security'));
        } else {
            $setup->setupDefaultUserTypes();
            $this->addFlash('success', 'user_types.setup.success');
        }

        return $this->redirectToRoute('user_types');
    }

    /**
     * @Route("/add", name="add_user_type")
     */
    public function add(Request $request, AttributePersister $attributePersister): Response {
        $userType = new UserType();

        $form = $this->createForm(UserTypeType::class, $userType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($userType);

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
     * @Route("/{uuid}/edit", name="edit_user_type")
     */
    public function edit(Request $request, UserType $userType, AttributePersister $attributePersister): Response {
        $form = $this->createForm(UserTypeType::class, $userType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($userType);

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserTypeAttributes($attributeData, $userType);

            $this->addFlash('success', 'user_types.edit.success');
            return $this->redirectToRoute('user_types');
        }

        return $this->render('user_types/edit.html.twig', [
            'form' => $form->createView(),
            'type' => $userType
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_user_type")
     */
    public function remove(UserType $userType, Request $request, UserTypeRepositoryInterface $userTypeRepository, TranslatorInterface $translator): Response {
        $this->denyAccessUnlessGranted(UserTypeVoter::REMOVE, $userType);

        if($userTypeRepository->countUsersOfUserType($userType) > 0) {
            $this->addFlash('error', $translator->trans('user_types.remove.error', [
                '%name%' => $userType->getName()
            ]));

            return $this->redirectToRoute('user_types');
        }

        $form = $this->createForm(ConfirmType::class, [], [
            'message' => $translator->trans('user_types.remove.confirm', [ '%name%' => $userType->getName() ]),
            //'help' => $translator->trans('user_types.remove.help'),
            'header' => 'user_types.remove.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($userType);

            $this->addFlash('success', 'user_types.remove.success');
            return $this->redirectToRoute('user_types');
        }

        return $this->render('user_types/remove.html.twig', [
            'form' => $form->createView(),
            'type' => $userType
        ]);
    }
}