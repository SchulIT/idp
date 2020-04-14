<?php

namespace App\Controller;

use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Form\AttributeDataTrait;
use App\Form\UserType;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use App\Saml\AttributeValueProvider;
use App\Service\AttributePersister;
use App\Service\AttributeResolver;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController {

    use AttributeDataTrait;

    const USERS_PER_PAGE = 25;

    private $repository;
    private $typeRepository;

    public function __construct(UserRepositoryInterface $repository, UserTypeRepositoryInterface $typeRepository) {
        $this->repository = $repository;
        $this->typeRepository = $typeRepository;
    }

    /**
     * @Route("/users", name="users")
     */
    public function index(Request $request) {
        $q = $request->query->get('q', null);
        $type = $request->query->get('type', null);

        $types = $this->typeRepository->findAll();

        $paginator = $this->repository->getPaginatedUsers(static::USERS_PER_PAGE, $page, $type, $q);

        $pages = 1;

        if($paginator->count() > 0) {
            $pages = ceil((float)$paginator->count() / static::USERS_PER_PAGE);
        }

        return $this->render('users/index.html.twig', [
            'users' => $paginator->getIterator(),
            'page' => $page,
            'pages' => $pages,
            'q' => $q,
            'types' => $types,
            'type' => $type
        ]);
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
}