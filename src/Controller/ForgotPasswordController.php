<?php

namespace App\Controller;

use App\Entity\PasswordResetToken;
use App\Repository\UserRepositoryInterface;
use App\Security\ForgotPassword\ForgotPasswordManager;
use App\Security\ForgotPassword\TokenExpiredException;
use App\Security\ForgotPassword\TooManyRequestsException;
use App\Security\ForgotPassword\UserCannotResetPasswordException;
use App\Security\PasswordStrengthHelper;
use SchulIT\CommonBundle\Helper\DateHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ForgotPasswordController extends AbstractController {

    private const CSRF_TOKEN_ID = 'forgot_pw';

    private const CSRF_TOKEN_KEY = '_csrf_token';

    public function __construct(private readonly ForgotPasswordManager $manager, private readonly CsrfTokenManagerInterface $csrfTokenManager, private readonly TranslatorInterface $translator)
    {
    }

    private function isCsrfTokenFromRequestValid(Request $request): bool {
        $tokenValue = $request->request->get(self::CSRF_TOKEN_KEY);
        $token = new CsrfToken(self::CSRF_TOKEN_ID, $tokenValue);

        return $this->csrfTokenManager->isTokenValid($token);
    }

    private function getCsrfTokenMessage(): string {
        return $this->translator->trans('Invalid CSRF token.', [], 'security');
    }

    #[Route(path: '/forgot_pw', name: 'forgot_password')]
    public function request(Request $request, UserRepositoryInterface $userRepository, TranslatorInterface $translator): Response {
        if($request->isMethod('POST') && ($request->request->has('_username') || $request->request->has('_email'))) {
            $user = null;

            if($request->request->has('_username')) {
                $username = $request->request->get('_username');
                $user = $userRepository->findOneByUsername($username);
            } else if($request->request->has('_email')) {
                $email = $request->request->get('_email');
                $user = $userRepository->findOneByEmail($email);
            } else {
                $this->addFlash('error', 'forgot_pw.request.username_empty');
                return $this->redirectToRoute('forgot_password', [ 'email' => $request->query->get('email')]);
            }

            if($this->isCsrfTokenFromRequestValid($request) !== true) {
                $this->addFlash('error', $this->getCsrfTokenMessage());
            } else if($user !== null) {
                try {
                    $token = $this->manager->createPasswordResetRequest($user, $user->getEmail());
                    $this->addFlash('success', $translator->trans('forgot_pw.request.success', [ '%expiry%' => $token->getExpiresAt()->format($translator->trans('date.with_time')) ]));

                    return $this->redirectToRoute('login');
                } catch (TooManyRequestsException) {
                    $this->addFlash('error', 'forgot_pw.request.too_many_requests');
                } catch(UserCannotResetPasswordException) {
                    $this->addFlash('error', 'forgot_pw.request.cannot_change');
                }
            }
        }

        return $this->render('auth/forgot_pw.html.twig', [
            'csrfTokenId' => self::CSRF_TOKEN_ID,
            'use_email' => $request->query->get('email') === '✓'
        ]);
    }

    #[Route(path: '/forgot_pw/{token}', name: 'change_password')]
    public function change(string $token, Request $request, PasswordStrengthHelper $passwordStrengthHelper, DateHelper $dateHelper): Response {
        $resetToken = $this->manager->getToken($token);

        if($resetToken === null) {
            return $this->render('auth/forgot_pw_error.html.twig', [
                'error' => 'forgot_pw.error.token_not_found'
            ]);
        } else if($resetToken->getExpiresAt() < $dateHelper->getNow()) {
            return $this->render('auth/forgot_pw_error.html.twig', [
                'error' => 'forgot_pw.error.token_expired'
            ]);
        }

        if($request->isMethod('POST')) {
            $password = $request->request->get('_password');
            $repeatPassword = $request->request->get('_repeat_password');

            $violations = $passwordStrengthHelper->validatePassword($password);

            if($this->isCsrfTokenFromRequestValid($request) !== true) {
                $this->addFlash('error', $this->getCsrfTokenMessage());
            } else if($violations->count() > 0) {
                // flashes are added in twig template
            } else if($password !== $repeatPassword) {
                $this->addFlash('error', 'forgot_pw.change.password_error');
            } else {
                try {
                    $this->manager->updatePassword($resetToken, $password);
                    $this->addFlash('success', 'forgot_pw.change.success');

                    return $this->redirectToRoute('login');
                } catch (TokenExpiredException) {
                    // this should not happen...
                    return $this->render('auth/forgot_pw_error.html.twig', [
                        'error' => 'forgot_pw.error.token_expired'
                    ]);
                }
            }
        }

        return $this->render('auth/change_pw.twig', [
            'user' => $resetToken->getUser(),
            'csrfTokenId' => self::CSRF_TOKEN_ID,
            'token' => $resetToken,
            'violations' => $violations ?? null
        ]);
    }
}