<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\EnableTwoFactorType;
use App\Repository\UserRepositoryInterface;
use App\Security\TwoFactor\BackupCodeGenerator;
use App\Security\Voter\ProfileVoter;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Trusted\TrustedDeviceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class TwoFactorController extends AbstractController
{
    public const TWO_FACTOR_EMAIL_CSRF_TOKEN = 'two-factor-csrf';
    public const GOOGLE_SECRET_KEY = 'google-code';
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }
    #[Route(path: '/profile/two_factor', name: 'two_factor')]
    public function twoFactorAuthentication(#[CurrentUser] User $user, Request $request, TrustedDeviceManager $trustedDeviceManager,
                                            FirewallMap $firewallMap, CsrfTokenManagerInterface $tokenManager): Response {
        $this->denyAccessUnlessGranted(ProfileVoter::USE_2FA);

        $isGoogleEnabled = $user->isGoogleAuthenticatorEnabled();
        $backupCodes = $user->getBackupCodes();

        $csrfToken = $tokenManager
            ->getToken(static::TWO_FACTOR_EMAIL_CSRF_TOKEN);

        $isTrustedDevice = $trustedDeviceManager->isTrustedDevice($user, $firewallMap->getFirewallConfig($request)->getName());

        return $this->render('profile/two_factor/index.html.twig', [
            'isGoogleEnabled' => $isGoogleEnabled,
            'backupCodes' => $backupCodes,
            'csrfToken' => $csrfToken,
            'isTrustedDevice' => $isTrustedDevice,
        ]);
    }
    #[Route(path: '/profile/two_factor/google/enable', name: 'enable_google_two_factor')]
    public function enableGoogleTwoFactorAuthentication(#[CurrentUser] User $user, Request $request, BackupCodeGenerator $backupCodeGenerator,
                                                        GoogleAuthenticatorInterface $googleAuthenticator): Response {
        $this->denyAccessUnlessGranted(ProfileVoter::USE_2FA);

        if($user->isGoogleAuthenticatorEnabled()) {
            return $this->redirectToRoute('two_factor');
        }

        $secret = $googleAuthenticator->generateSecret();
        $form = $this->createForm(EnableTwoFactorType::class, [
            'secret' => $secret
        ]);
        $form->handleRequest($request);

        /*
         * setup fake user to get QR code content
         */
        $secret = $form->get('secret')->getData();
        $fakeUser = (new User())
            ->setUsername($user->getUsername());
        $fakeUser->setGoogleAuthenticatorSecret($secret);
        $qrContent = $googleAuthenticator->getQRContent($fakeUser);

        if($form->isSubmitted() && $form->isValid()) {
            $code = $form->get('code')->getData();

            if($googleAuthenticator->checkCode($fakeUser, $code)) {
                $user->setGoogleAuthenticatorSecret($secret);
                $user->setBackupCodes($backupCodeGenerator->generateCodes());

                $this->userRepository->persist($user);

                $this->addFlash('success', 'two_factor.google.enable.success');
                return $this->redirectToRoute('two_factor');
            } else {
                $form->get('code')
                    ->addError(new FormError('two_factor.google.enable.wrong_code'));
            }
        }

        return $this->render('profile/two_factor/enable_google.html.twig', [
            'form' => $form->createView(),
            'secret' => $secret,
            'qrContent' => $qrContent
        ]);
    }
    #[Route(path: '/profile/two_factor/google/codes/regenerate', name: 'regenerate_backup_codes', methods: ['POST'])]
    public function regenerateBackupCodes(#[CurrentUser] User $user, Request $request, BackupCodeGenerator $backupCodeGenerator): Response {
        $this->denyAccessUnlessGranted(ProfileVoter::USE_2FA);

        $token = $request->request->get('_csrf_token');

        if(!$user->isGoogleAuthenticatorEnabled()) {
            return $this->redirectToRoute('two_factor');
        }

        if(!$this->isCsrfTokenValid(static::TWO_FACTOR_EMAIL_CSRF_TOKEN, $token)) {
            $this->addFlash('error', 'two_factor.invalid_csrf');
            return $this->redirectToRoute('two_factor');
        }

        $user->setBackupCodes($backupCodeGenerator->generateCodes());

        $this->userRepository->persist($user);

        $this->addFlash('success', 'two_factor.google.backup.regenreate_success');
        return $this->redirectToRoute('two_factor');
    }
    #[Route(path: '/profile/two_factor/google/disable', name: 'disable_google_two_factor', methods: ['POST'])]
    public function disableGoogleTwoFactorAuthentication(#[CurrentUser] User $user, Request $request): Response {
        $this->denyAccessUnlessGranted(ProfileVoter::USE_2FA);

        $token = $request->request->get('_csrf_token');

        if(!$this->isCsrfTokenValid(static::TWO_FACTOR_EMAIL_CSRF_TOKEN, $token)) {
            $this->addFlash('error', 'two_factor.invalid_csrf');
            return $this->redirectToRoute('two_factor');
        }

        $user->setGoogleAuthenticatorSecret(null);
        $user->emptyBackupCodes();

        $this->userRepository->persist($user);

        $this->addFlash('success', 'two_factor.google.disable.success');
        return $this->redirectToRoute('two_factor');
    }
}
