<?php

namespace App\Controller;

use App\Form\UserProfileCompleteType;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Security\Registration\CodeAlreadyRedeemedException;
use App\Security\Registration\RegistrationCodeManager;
use App\Settings\RegistrationSettings;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/register')]
class RegistrationController extends AbstractController {

    private const CSRF_TOKEN_KEY = '_csrf_token';
    private const CSRF_TOKEN_ID = 'registration';

    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    #[Route(path: '/redeem', name: 'redeem_registration_code')]
    public function redeem(Request $request, RegistrationCodeRepositoryInterface $codeRepository, RegistrationCodeManager $manager,
                           DateHelper $dateHelper, TranslatorInterface $translator): Response {
        if(!$request->isMethod('POST')) {
            return $this->redirectToRoute('login');
        }

        $csrfToken = $request->request->get(self::CSRF_TOKEN_KEY);

        if ($this->isCsrfTokenValid(self::CSRF_TOKEN_ID, $csrfToken) !== true) {
            $this->addFlash('error', $this->getCsrfTokenMessage());
            return $this->redirectToRoute('login');
        }

        $registrationCode = $request->request->get('code');

        if(empty($registrationCode)) {
            $this->addFlash('error', 'register.redeem.error.invalid_request');
            return $this->redirectToRoute('login');
        }

        $code = $codeRepository->findOneByCode($registrationCode);

        if($code === null) {
            $this->addFlash('error', 'registration.not_found');
            return $this->redirectToRoute('login');
        }

        if($manager->isRedeemed($code)) {
            $this->addFlash('error', 'registration.already_redeemed');
            return $this->redirectToRoute('login');
        }

        if($code->getValidFrom() !== null && $code->getValidFrom() > $dateHelper->getToday()) {
            $this->addFlash('error', $translator->trans('register.redeem.error.not_yet_valid', [
                '%date%' => $code->getValidFrom()->format($translator->trans('date.format'))
            ], 'security'));
            return $this->redirectToRoute('login');
        }

        return $this->render('register/redeem.html.twig', [
            'code' => $code
        ]);
    }

    #[Route(path: '/complete', name: 'register')]
    public function register(Request $request, RegistrationSettings $settings, RegistrationCodeRepositoryInterface $codeRepository,
                             RegistrationCodeManager $manager): Response {
        $registrationCode = $request->request->get('code');

        if(empty($registrationCode)) {
            $this->addFlash('error', 'register.redeem.error.invalid_request');
            return $this->redirectToRoute('login');
        }

        $code = $codeRepository->findOneByCode($registrationCode);

        if($code === null) {
            $this->addFlash('error', 'register.redeem.error.not_found');
            return $this->redirectToRoute('login');
        }

        $user = $manager->getTemplateUser();
        $form = $this->createForm(UserProfileCompleteType::class, $user, [
            'username_suffix' => sprintf('@%s', $settings->getUsernameSuffix())
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            try {
                $manager->complete($code, $user, $form->get('password')->getData());
                $this->addFlash('success', 'registration.success');

                if($user->isEmailConfirmationPending()) {
                    $this->addFlash('info', 'registration.email_confirmation_pending');
                }
            } catch (CodeAlreadyRedeemedException) {
                $this->addFlash('error', 'registration.already_redeemed');
            }

            return $this->redirectToRoute('login');
        }

        return $this->render('register/complete.html.twig', [
            'code' => $code,
            'form' => $form->createView()
        ]);
    }

    private function getCsrfTokenMessage(): string {
        return $this->translator->trans('Invalid CSRF token.', [], 'security');
    }
}