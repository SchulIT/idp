<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\EmailConfirmation;
use App\Entity\RegistrationCode;
use App\Entity\User;
use App\Form\LinkStudentType;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\EmailConfirmation\ConfirmationManager;
use App\Security\Session\ActiveSessionsResolver;
use App\Security\Session\LogoutHelper;
use App\Security\Voter\LinkStudentVoter;
use App\Service\UserServiceProviderResolver;
use DateTime;
use Exception;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardController extends AbstractController {

    public const CsrfTokenId = 'send_confirmation';

    #[Route(path: '')]
    #[Route(path: '/')]
    public function redirectToDashboard(): Response {
        return $this->redirectToRoute('dashboard');
    }

    #[Route(path: '/dashboard', name: 'dashboard')]
    public function dashboard(#[CurrentUser] User $user, Request $request, UserServiceProviderResolver $resolver, ActiveSessionsResolver $sessionsResolver): Response {
        $services = $resolver->getServicesForCurrentUser();

        $form = null;

        if($this->isGranted(LinkStudentVoter::LINK)) {
            $form = $this->createForm(LinkStudentType::class);
            $form->handleRequest($request);
        }

        return $this->render('dashboard/index.html.twig', [
            'services' => $services,
            'students' => $user->getLinkedStudents(),
            'links_required' => $user->getType()->isCanLinkStudents() && $user->getLinkedStudents()->count() === 0,
            'form' => $form?->createView(),
            'sessions' => $sessionsResolver->getSessionsForUser($user),
            'token_id' => self::CsrfTokenId
        ]);
    }

    #[Route(path: '/confirmation', name: 'send_pending_email_confirmation', methods: ['POST'])]
    public function sendEmailConfirmation(#[CurrentUser] User $user, Request $request, ConfirmationManager $confirmationManager, TranslatorInterface $translator): Response {
        if(!$user->getEmailConfirmation() instanceof EmailConfirmation) {
            return $this->redirectToDashboard();
        }

        if(!$this->isCsrfTokenValid(self::CsrfTokenId, $request->request->get('_csrf_token'))) {
            $this->addFlash('error', $translator->trans('Invalid CSRF token.', [], 'security'));
            return $this->redirectToDashboard();
        }

        $confirmationManager->newConfirmation($user, $user->getEmailConfirmation()->getEmailAddress());
        $this->addFlash('success', 'dashboard.send_email_confirmation.success');

        return $this->redirectToDashboard();
    }

    #[Route(path: '/link', name: 'link_student')]
    public function index(#[CurrentUser] User $user, Request $request, UserRepositoryInterface $userRepository, DateHelper $dateHelper,
                          RegistrationCodeRepositoryInterface $codeRepository, TranslatorInterface $translator): Response {
        $this->denyAccessUnlessGranted(LinkStudentVoter::LINK);

        $form = $this->createForm(LinkStudentType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && !empty($form->get('code')->getData())) {
            $code = $codeRepository->findOneByCode($form->get('code')->getData());

            if($code instanceof RegistrationCode) {
                if ($code->getValidFrom() instanceof DateTime && $code->getValidFrom() > $dateHelper->getToday()) {
                    $this->addFlash('error', $translator->trans('register.redeem.error.not_yet_valid', [
                        '%date%' => $code->getValidFrom()->format($translator->trans('date.format'))
                    ], 'security'));
                } elseif ($user->getLinkedStudents()->contains($code->getStudent())) {
                    $this->addFlash('error', 'link.student.error.already_linked');
                } if($code->getRedeemingUser() instanceof User) {
                    $this->addFlash('error', $translator->trans('register.redeem.error.already_redeemed', [], 'security'));
                } else {
                    $user->addLinkedStudent($code->getStudent());
                    $code->setRedeemingUser($user);

                    $userRepository->persist($user);
                    $codeRepository->persist($code);

                    $this->addFlash('success', 'link.student.success');
                }
            } else {
                $this->addFlash('error', $translator->trans('register.redeem.error.not_found', [], 'security'));
            }
        }

        return $this->redirectToRoute('dashboard');
    }


    #[Route('/destroy', name: 'destroy_sessions')]
    public function destroySessions(#[CurrentUser] User $user, LogoutHelper $helper): Response {
        $helper->logout($user);

        return $this->redirect('/');
    }
}
