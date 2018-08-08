<?php

namespace App\Controller;

use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Entity\UserType as UserTypeEntity;
use App\Form\AttributeDataTrait;
use App\Form\UserType;
use App\Saml\AttributeValueProvider;
use App\Service\AttributePersister;
use App\Service\AttributeResolver;
use Doctrine\ORM\Tools\Pagination\Paginator;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class UserController extends Controller {

    use AttributeDataTrait;

    const USERS_PER_PAGE = 25;

    /**
     * @Route("/users", name="users")
     */
    public function index(Request $request) {
        $q = $request->query->get('q', null);
        $type = $request->query->get('type', null);

        $em = $this->getDoctrine()->getManager();

        /** @var UserTypeEntity[] $types */
        $types = $em->getRepository(UserTypeEntity::class)
            ->findAll();

        $query = $em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.username', 'asc');

        if(!empty($q)) {
            $query
                ->andWhere(
                    $query->expr()->orX(
                        'u.username LIKE :query',
                        'u.firstname LIKE :query',
                        'u.lastname LIKE :query',
                        'u.email LIKE :query'
                    )
                )
                ->setParameter('query', '%' . $q . '%');
        }

        if(!empty($type)) {
            $query
                ->andWhere(
                    'u.type = :type'
                )
                ->setParameter('type', $type);
        }

        $page = $request->query->get('p', 1);
        if(!is_numeric($page) || $page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * static::USERS_PER_PAGE;

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setMaxResults(static::USERS_PER_PAGE)
            ->setFirstResult($offset);

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
     * @Route("/users/{id}/attributes", name="show_attributes")
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
    public function add(Request $request, AttributePersister $attributePersister) {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $passwordEncoder = $this->get('security.password_encoder');
            $user->setPassword($passwordEncoder->encodePassword($user, $form->get('group_password')->get('password')->getData()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

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
     * @Route("/users/{id}/edit", name="edit_user")
     */
    public function edit(Request $request, User $user, AttributePersister $attributePersister) {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('group_password')->get('password')->getData();

            if(!empty($password) && !$user instanceof ActiveDirectoryUser) {
                $passwordEncoder = $this->get('security.password_encoder');
                $user->setPassword($passwordEncoder->encodePassword($user, $password));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserAttributes($attributeData, $user);

            $this->addFlash('success', 'users.edit.success');
            return $this->redirectToRoute('users');
        }

        return $this->render('users/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/{id}/remove", name="remove_user")
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
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'users.remove.success');
            return $this->redirectToRoute('users');
        }

        return $this->render('users/remove.html.twig', [
            'form' => $form->createView()
        ]);
    }
}