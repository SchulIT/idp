<?php

declare(strict_types=1);

namespace App\Controller;

use AdAuth\AdAuthInterface;
use AdAuth\Credentials;
use AdAuth\Response\PasswordFailedResponse;
use AdAuth\Response\PasswordSuccessResponse;
use AdAuth\SocketException;
use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Form\AttributeDataTrait;
use App\Form\ResetPasswordType;
use App\Form\ResetPasswortActiveDirectoryType;
use App\Form\UserType;
use App\Entity\UserType as UserTypeEntity;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use App\Saml\AttributeValueProvider;
use App\Security\ForgotPassword\ForgotPasswordManager;
use App\Security\ForgotPassword\TooManyRequestsException;
use App\Security\ForgotPassword\UserCannotResetPasswordException;
use App\Security\Session\LogoutHelper;
use App\Service\AttributePersister;
use App\Service\AttributeResolver;
use App\Utils\ArrayUtils;
use App\View\Filter\UserRoleFilter;
use App\View\Filter\UserTypeFilter;
use DateTime;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(
    new Expression(
        "is_granted('ROLE_ADMIN') or is_granted('ROLE_PASSWORD_MANAGER')"
    )
)]
class UserController extends AbstractController {

    use AttributeDataTrait;

    public const USERS_PER_PAGE = 25;

    public const CsrfTokenId = 'remove_user';
    public const CsrfTokenKey = '_csrf_token';

    public function __construct(private readonly UserRepositoryInterface $repository)
    {
    }

    private function internalDisplay(Request $request, UserTypeFilter $typeFilter, UserRoleFilter $roleFilter, bool $deleted): Response {
        $q = $request->query->get('q', null);
        $page = $request->query->getInt('page', 1);
        $grade = $request->query->get('grade', null);

        $grades = $this->repository->findGrades();

        if(!in_array($grade, $grades)) {
            $grade = null;
        }

        $typeFilterView = $typeFilter->handle($request->query->get('type'));
        $roleFilterView = $roleFilter->handle($request->query->get('role'));

        $withoutParents = $typeFilterView->getCurrentType() !== null && $typeFilterView->getCurrentType()->getAlias() === 'student' && $request->query->get('without_parents') === '✓';

        $paginator = $this->repository->getPaginatedUsers(static::USERS_PER_PAGE, $page, $typeFilterView->getCurrentType(), $roleFilterView->getCurrentRole(), $q, $grade, $deleted, $withoutParents);

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
            'grade' => $grade,
            'grades' => $grades,
            'q' => $q,
            'statistics' => $statistics,
            'count' => $this->repository->countUsers(),
            'roleFilter' => $roleFilterView,
            'typeFilter' => $typeFilterView,
            'withoutParents' => $withoutParents,
            'csrf_id' => static::CsrfTokenId,
            'csrf_key' => static::CsrfTokenKey
        ]);
    }

    #[Route(path: '/users', name: 'users')]
    public function index(Request $request, UserTypeFilter $typeFilter, UserRoleFilter $roleFilter): Response {
        return $this->internalDisplay($request, $typeFilter, $roleFilter, false);
    }

    #[Route(path: '/users/trash', name: 'users_trash')]
    #[IsGranted('ROLE_ADMIN')]
    public function trash(Request $request, UserTypeFilter $typeFilter, UserRoleFilter $roleFilter): Response {
        return $this->internalDisplay($request, $typeFilter, $roleFilter, true);
    }

    #[Route(path: '/users/trash/empty', name: 'empty_users_trash')]
    #[IsGranted('ROLE_ADMIN')]
    public function emptyTrash(Request $request, UserRepositoryInterface $userRepository, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, [], [
            'message' => 'users.trash.remove_all.confirm'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $count = $userRepository->removeDeletedUsers();
            $this->addFlash('success', $translator->trans('users.trash.remove_all.success', [ '%count%' => $count]));

            return $this->redirectToRoute('users_trash');
        }

        return $this->render('users/empty_trash.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/users/{uuid}/attributes', name: 'show_attributes')]
    #[IsGranted('ROLE_ADMIN')]
    public function showAttributes(#[MapEntity(mapping: ['uuid' => 'uuid'])] User $user, AttributeResolver $resolver, AttributeValueProvider $provider): Response {
        $attributes = $resolver->getDetailedResultingAttributeValuesForUser($user);
        $defaultAttributes = $provider->getCommonAttributesForUser($user);

        return $this->render('users/attributes.html.twig', [
            'selectedUser' => $user,
            'attributes' => $attributes,
            'defaultAttributes' => $defaultAttributes
        ]);
    }

    #[Route(path: '/users/add', name: 'add_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, AttributePersister $attributePersister, UserPasswordHasherInterface $passwordHasher): Response {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('group_password')->get('password')->getData()));

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

    #[Route(path: '/users/{uuid}/edit', name: 'edit_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] User $user, AttributePersister $attributePersister, UserPasswordHasherInterface $passwordHasher): Response {
        if($user->isDeleted()) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($form->has('group_password')) {
                $password = $form->get('group_password')->get('password')->getData();

                if (!empty($password) && !$user instanceof ActiveDirectoryUser) {
                    $user->setPassword($passwordHasher->hashPassword($user, $password));
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

    #[Route(path: '/users/{uuid}/remove', name: 'remove_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function remove(#[CurrentUser] User $currentUser, #[MapEntity(mapping: ['uuid' => 'uuid'])] User $user, Request $request, TranslatorInterface $translator): Response {
        if($currentUser->getId() === $user->getId()) {
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

    #[Route(path: '/users/{uuid}/restore', name: 'restore_user', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function restore(#[CurrentUser] User $currentUser, #[MapEntity(mapping: ['uuid' => 'uuid'])]  User $user, Request $request): Response {
        if($currentUser->getId() === $user->getId()) {
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

    #[Route(path: '/users/{uuid}/reset_password_ad', name: 'reset_password_ad')]
    #[IsGranted('ROLE_PASSWORD_MANAGER')]
    public function resetPasswordActiveDirectory(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] User $user, AdAuthInterface $adAuth, TranslatorInterface $translator): Response {
        if(!$user instanceof ActiveDirectoryUser) {
            $this->addFlash('error', 'users.reset_pw_ad.cannot_change');
            return $this->redirectToRoute('users');
        }

        $form = $this->createForm(ResetPasswortActiveDirectoryType::class, [
            'username' => $user->getUserIdentifier()
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $adminUsername = $form->get('admin_username')->getData();
            $adminPassword = $form->get('admin_password')->getData();
            $username = $user->getUserIdentifier();
            $password = $form->get('password')->getData();

            try {
                $response = $adAuth->resetPassword(
                    new Credentials($username, $password),
                    new Credentials($adminUsername, $adminPassword)
                );

                if ($response instanceof PasswordSuccessResponse) {
                    $this->addFlash('success', 'users.reset_pw_ad.success');
                    return $this->redirectToRoute('users');
                } elseif ($response instanceof PasswordFailedResponse) {
                    $this->addFlash('error', $translator->trans('users.reset_pw_ad.failure', [
                        '%error%' => $response->getResult()
                    ]));
                }
            } catch (SocketException $e) {
                $this->addFlash('error', $translator->trans('users.reset_pw_ad.failure', [
                    '%error%' => $e->getMessage()
                ]));
            }
        }

        return $this->render('users/reset_pw_ad.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/users/{uuid}/reset_password', name: 'reset_password')]
    #[IsGranted('ROLE_PASSWORD_MANAGER')]
    public function resetPassword(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] User $user, ForgotPasswordManager $manager, TranslatorInterface $translator): Response {
        $form = $this->createForm(ResetPasswordType::class, [
            'email' => $user->getEmail()
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            try {
                $token = $manager->createPasswordResetRequest($user, $email);
                $this->addFlash('success', $translator->trans('forgot_pw.request.success', ['%expiry%' => $token->getExpiresAt()->format($translator->trans('date.with_time'))]));
                return $this->redirectToRoute('users');
            } catch (UserCannotResetPasswordException) {
                $this->addFlash('error', 'forgot_pw.request.cannot_change');
                return $this->redirectToRoute('users');
            } catch (TooManyRequestsException) {
                $this->addFlash('error', 'forgot_pw.request.too_many_requests');
                return $this->redirectToRoute('users');
            }
        }

        return $this->render('users/reset_pw.html.twig', [
            'user' => $user,
            'form'=> $form->createView()
        ]);
    }

    #[Route('/users/{uuid}/logout', name: 'user_logout_everywhere')]
    #[IsGranted('ROLE_ADMIN')]
    public function logout(#[MapEntity(mapping: ['uuid' => 'uuid'])] User $user, LogoutHelper $logoutHelper, RefererHelper $refererHelper): Response {
        $logoutHelper->logout($user);

        $this->addFlash('success', 'sessions.logout_everywhere.success');
        return $this->redirect(
            $refererHelper->getRefererPathFromRequest('users')
        );
    }
}
