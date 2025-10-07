<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserRole;
use App\Form\AttributeDataTrait;
use App\Form\UserRoleType;
use App\Repository\UserRoleRepositoryInterface;
use App\Service\AttributePersister;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserRoleController extends AbstractController
{
    use AttributeDataTrait;
    public function __construct(private UserRoleRepositoryInterface $repository)
    {
    }
    #[Route(path: '/admin/user_roles', name: 'user_roles')]
    public function index(): Response {
        $roles = $this->repository
            ->findAll();

        return $this->render('user_roles/index.html.twig', [
            'roles' => $roles
        ]);
    }
    #[Route(path: '/admin/user_roles/add', name: 'add_role')]
    public function add(Request $request, AttributePersister $attributePersister): Response {
        $role = new UserRole();

        $form = $this->createForm(UserRoleType::class, $role);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($role);

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserRoleAttributes($attributeData, $role);

            $this->addFlash('success', 'user_roles.add.success');
            return $this->redirectToRoute('user_roles');
        }

        return $this->render('user_roles/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[NotFoundRedirect(redirectRoute: 'user_roles', flashMessage: 'user_roles.not_found')]
    #[Route(path: '/admin/user_roles/{uuid}/edit', name: 'edit_role')]
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] UserRole $role, AttributePersister $attributePersister): Response {
        $form = $this->createForm(UserRoleType::class, $role);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($role);

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserRoleAttributes($attributeData, $role);

            $this->addFlash('success', 'user_roles.edit.success');
            return $this->redirectToRoute('user_roles');
        }

        return $this->render('user_roles/edit.html.twig', [
            'form' => $form->createView(),
            'role' => $role
        ]);
    }

    #[NotFoundRedirect(redirectRoute: 'user_roles', flashMessage: 'user_roles.not_found')]
    #[Route(path: '/admin/user_roles/{uuid}/remove', name: 'remove_role')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] UserRole $role, Request $request, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, [ ], [
            'message' => $translator->trans('user_roles.remove.confirm', [
                '%name%' => $role->getName()
            ]),
            'label' => 'user_roles.remove.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($role);

            $this->addFlash('success', 'user_roles.remove.success');
            return $this->redirectToRoute('user_roles');
        }

        return $this->render('user_roles/remove.html.twig', [
            'form' => $form->createView(),
            'role' => $role
        ]);
    }
}
