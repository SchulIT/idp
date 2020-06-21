<?php

namespace App\Controller;

use App\Entity\PasswordResetToken;
use App\Repository\UserRepositoryInterface;
use App\Security\ForgotPassword\ForgotPasswordManager;
use App\Security\PasswordStrengthHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ForgotPasswordController extends AbstractController {

    private const CSRF_TOKEN_ID = 'forgot_pw';

    private const CSRF_TOKEN_KEY = '_csrf_token';
    private const USERNAME_KEY = '_username';
    private const PASSWORD_KEY = '_password';
    private const REPEAT_PASSWORD_KEY = '_repeat_password';

    private $manager;
    private $csrfTokenManager;
    private $translator;

    public function __construct(ForgotPasswordManager $manager, CsrfTokenManagerInterface $csrfTokenManager, TranslatorInterface $translator) {
        $this->manager = $manager;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->translator = $translator;
    }

    private function isCsrfTokenFromRequestValid(Request $request): bool {
        $tokenValue = $request->request->get(static::CSRF_TOKEN_KEY);
        $token = new CsrfToken(static::CSRF_TOKEN_ID, $tokenValue);

        return $this->csrfTokenManager->isTokenValid($token);
    }

    private function getCsrfTokenMessage(): string {
        return $this->translator->trans('Invalid CSRF token.', [], 'security');
    }

    /**
     * @Route("/forgot_pw", name="forgot_password")
     */
    public function request(Request $request, CsrfTokenManagerInterface $csrfTokenManager, TranslatorInterface $translator, UserRepositoryInterface $userRepository) {
        if($request->isMethod('POST')) {
            $username = $request->request->get('_username');
            $user = $userRepository->findOneByUsername($username);

            if($this->isCsrfTokenFromRequestValid($request) !== true) {
                $this->addFlash('error', $this->getCsrfTokenMessage());
            } else if ($username === null) {
                $this->addFlash('error', 'forgot_pw.request.username_empty');
            } else if($user !== null && $this->manager->canResetPassword($user) !== true) {
                $this->addFlash('error', 'forgot_pw.request.cannot_change');
                return $this->redirectToRoute('login');
            } else {
                $this->manager->resetPassword($user);
                $this->addFlash('success', 'forgot_pw.request.success');

                return $this->redirectToRoute('login');
            }
        }

        return $this->render('auth/forgot_pw.html.twig', [
            'csrfTokenId' => static::CSRF_TOKEN_ID
        ]);
    }

    /**
     * @Route("/forgot_pw/{token}", name="change_password")
     * @ParamConverter("token", options={"mapping": {"token": "token"}})
     */
    public function change(PasswordResetToken $token, Request $request, PasswordStrengthHelper $passwordStrengthHelper) {
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
                $this->manager->updatePassword($token, $password);
                $this->addFlash('success', 'forgot_pw.change.success');

                return $this->redirectToRoute('login');
            }
        }

        return $this->render('auth/change_pw.twig', [
            'user' => $token->getUser(),
            'csrfTokenId' => static::CSRF_TOKEN_ID,
            'token' => $token,
            'violations' => $violations ?? null
        ]);
    }
}