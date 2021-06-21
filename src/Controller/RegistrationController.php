<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileCompleteType;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use App\Security\Registration\CodeAlreadyRedeemedException;
use App\Security\Registration\CodeNotFoundException;
use App\Security\Registration\EmailAlreadyExistsException;
use App\Security\Registration\EmailDomainNotAllowedException;
use App\Security\Registration\RegistrationCodeManager;
use App\Security\Registration\TokenNotFoundException;
use App\Security\UserAuthenticator;
use App\Settings\RegistrationSettings;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/register")
 */
class RegistrationController extends AbstractController {

    private const CSRF_TOKEN_KEY = '_csrf_token';
    private const CSRF_TOKEN_ID = 'registration';

    private $manager;
    private $translator;

    public function __construct(RegistrationCodeManager $manager, TranslatorInterface $translator) {
        $this->manager = $manager;
        $this->translator = $translator;
    }

    /**
     * @Route("/redeem", name="redeem_registration_code")
     */
    public function redeem(Request $request, RegistrationCodeRepositoryInterface $codeRepository, RegistrationCodeManager $manager,
                           DateHelper $dateHelper, TranslatorInterface $translator): Response {
        if(!$request->isMethod('POST')) {
            return $this->redirectToRoute('login');
        }

        $csrfToken = $request->request->get(static::CSRF_TOKEN_KEY);

        if ($this->isCsrfTokenValid(static::CSRF_TOKEN_ID, $csrfToken) !== true) {
            $this->addFlash('error', $this->getCsrfTokenMessage());
            return $this->redirectToRoute('login');
        }

        $code = $codeRepository->findOneByCode($request->request->get('code'));

        if($code === null) {
            $this->addFlash('error', 'register.redeem.error.not_found');
            return $this->redirectToRoute('login');
        }

        if($manager->isRedeemed($code)) {
            $this->addFlash('error', 'register.redeem.error.already_redeemed');
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

    /**
     * @Route("/complete", name="register")
     */
    public function register(Request $request, RegistrationSettings $settings, RegistrationCodeRepositoryInterface $codeRepository,
                             RegistrationCodeManager $manager, UserAuthenticator $authenticator, GuardAuthenticatorHandler $authenticatorHandler) {
        $code = $codeRepository->findOneByCode($request->request->get('code'));

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
            $manager->complete($code, $user, $form->get('password')->getData());

            $this->addFlash('success', 'register.completed');

            return $authenticatorHandler
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'secured'
                );
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