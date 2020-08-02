<?php

namespace App\Controller;

use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Form\AttributeDataTrait;
use App\Form\UserType;
use App\Entity\UserType as UserTypeEntity;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use App\Saml\AttributeValueProvider;
use App\Service\AttributePersister;
use App\Service\AttributeResolver;
use App\Utils\ArrayUtils;
use App\View\Filter\UserRoleFilter;
use App\View\Filter\UserTypeFilter;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController {

    use AttributeDataTrait;

    const USERS_PER_PAGE = 25;

    const CsrfTokenId = 'remove_user';
    const CsrfTokenKey = '_csrf_token';

    private $repository;
    private $typeRepository;

    public function __construct(UserRepositoryInterface $repository, UserTypeRepositoryInterface $typeRepository) {
        $this->repository = $repository;
        $this->typeRepository = $typeRepository;
    }

    private function internalDisplay(Request $request, UserTypeFilter $typeFilter, UserRoleFilter $roleFilter, bool $deleted) {
        $q = $request->query->get('q', null);
        $page = $request->query->getInt('page', 1);

        $typeFilterView = $typeFilter->handle($request->query->get('type'));
        $roleFilterView = $roleFilter->handle($request->query->get('role'));

        $paginator = $this->repository->getPaginatedUsers(static::USERS_PER_PAGE, $page, $typeFilterView->getCurrentType(), $roleFilterView->getCurrentRole(), $q, $deleted);

        $pages = 1;

        if($paginator->count() > 0) {
            $pages = ceil((float)$paginator->count() / static::USERS_PER_PAGE);
        }

        $template = $deleted ? 'users/trash.html.twig' : 'users/index.html.twig';

        $statistics = [ ];

        if($deleted === false) {
            foreach ($typeFilterView->getTypes() as $type) {
                $statistics[] = [
                    'type' => $type,
                    'count' => $this->repository->countUsers($type)
                ];
            }
        }

        return $this->render($template, [
            'users' => $paginator->getIterator(),
            'page' => $page,
            'pages' => $pages,
            'q' => $q,
            'statistics' => $statistics,
            'count' => $this->repository->countUsers(),
            'roleFilter' => $roleFilterView,
            'typeFilter' => $typeFilterView,
            'csrf_id' => static::CsrfTokenId,
            'csrf_key' => static::CsrfTokenKey
        ]);
    }

    /**
     * @Route("/users", name="users")
     */
    public function index(Request $request, UserTypeFilter $typeFilter, UserRoleFilter $roleFilter) {
        return $this->internalDisplay($request, $typeFilter, $roleFilter, false);
    }

    /**
     * @Route("/users/trash", name="users_trash")
     */
    public function trash(Request $request, UserTypeFilter $typeFilter, UserRoleFilter $roleFilter) {
        return $this->internalDisplay($request, $typeFilter, $roleFilter, true);
    }

    /**
     * @Route("/users/{uuid}/attributes", name="show_attributes")
     */
    public function showAttributes(User $user, AttributeResolver $resolver, AttributeValueProvider $provider) {
        $attributes = $resolver->getDetailedResultingAttributeValuesForUser($user);
        $defaultAttributes = $provider->getCommonAttributesForUser($user);

        return $this->render('users/attributes.html.twig', [
            'selectedUser' => $user,
            'attributes' => $attributes,
            'defaultAttributes' => $defaultAttributes
        ]);
    }

    /**
     * @Route("/users/add", name="add_user")
     */
    public function add(Request $request, AttributePersister $attributePersister, UserPasswordEncoderInterface $passwordEncoder) {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword($user, $form->get('group_password')->get('password')->getData()));

            $this->repository->persist($user);

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserAttributes($attributeData, $user);

            $this->addFlash('success', 'users.add.success');
            return $this->redirectToRoute('users');
        }

        return $this->render('users/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/{uuid}/edit", name="edit_user")
     */
    public function edit(Request $request, User $user, AttributePersister $attributePersister, UserPasswordEncoderInterface $passwordEncoder) {
        if($user->isDeleted()) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($form->has('group_password')) {
                $password = $form->get('group_password')->get('password')->getData();

                if (!empty($password) && !$user instanceof ActiveDirectoryUser) {
                    $user->setPassword($passwordEncoder->encodePassword($user, $password));
                }
            }

            $this->repository->persist($user);

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserAttributes($attributeData, $user);

            $this->addFlash('success', 'users.edit.success');
            return $this->redirectToRoute('users');
        }

        return $this->render('users/edit.html.twig', [
            'form' => $form->createView(),
            'requestedUser' => $user
        ]);
    }

    /**
     * @Route("/users/{uuid}/remove", name="remove_user")
     */
    public function remove(User $user, Request $request, TranslatorInterface $translator) {
        if($this->getUser() instanceof User && $this->getUser()->getId() === $user->getId()) {
            $this->addFlash('error', 'users.remove.error.self');
            return $this->redirectToRoute('users');
        }

        if($user->isDeleted() === false) {
            if($request->isMethod('POST') && $this->isCsrfTokenValid(static::CsrfTokenId, $request->request->get(static::CsrfTokenKey))) {
                $this->addFlash('success', 'users.remove.trash.success');
                $this->repository->remove($user);
                return $this->redirectToRoute('users');
            } else {
                $this->addFlash('error', 'users.remove.trash.error');
                return $this->redirectToRoute('users');
            }
        }

        $form = $this->createForm(ConfirmType::class, [], [
            'message' => $translator->trans('users.remove.confirm', [
                '%username%' => $user->getUsername(),
                '%firstname%' => $user->getFirstname(),
                '%lastname%' => $user->getLastname()
            ]),
            'label' => 'users.remove.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($user);

            $this->addFlash('success', 'users.remove.success');
            return $this->redirectToRoute('users');
        }

        return $this->render('users/remove.html.twig', [
            'form' => $form->createView(),
            'requestedUser' => $user
        ]);
    }

    /**
     * @Route("/users/{uuid}/restore", name="restore_user", methods={"POST"})
     */
    public function restore(User $user, Request $request) {
        if($this->getUser() instanceof User && $this->getUser()->getId() === $user->getId()) {
            $this->addFlash('error', 'users.restore.error.self');
            return $this->redirectToRoute('users');
        }

        if($request->isMethod('POST') && $this->isCsrfTokenValid(static::CsrfTokenId, $request->request->get(static::CsrfTokenKey))) {
            $this->addFlash('success', 'users.trash.restore.success');
            $user->setDeletedAt(null);
            $this->repository->persist($user);
            return $this->redirectToRoute('users_trash');
        }

        $this->addFlash('error', 'users.trash.restore.error');
        return $this->redirectToRoute('users_trash');
    }
}