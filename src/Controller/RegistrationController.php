<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileCompleteType;
use App\Security\Registration\CodeAlreadyRedeemedException;
use App\Security\Registration\CodeNotFoundException;
use App\Security\Registration\EmailAlreadyExistsException;
use App\Security\Registration\EmailDomainNotAllowedException;
use App\Security\Registration\RegistrationCodeManager;
use App\Security\Registration\TokenNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function redeem(Request $request): Response {
        if($request->isMethod('POST')) {
            $csrfToken = $request->request->get(static::CSRF_TOKEN_KEY);

            if ($this->isCsrfTokenValid(static::CSRF_TOKEN_ID, $csrfToken) !== true) {
                $this->addFlash('error', $this->getCsrfTokenMessage());
            }

            try {
                $this->manager->redeem($request->request->get('_code'));
                return $this->redirectToRoute('complete_registration_code');
            } catch (CodeAlreadyRedeemedException $e) {
                $this->addFlash('error', 'register.redeem.error.already_redeemed');
            } catch (CodeNotFoundException $e) {
                $this->addFlash('error', 'register.redeem.error.not_found');
            }
        }

        return $this->render('register/redeem.html.twig', [
            'csrf_token_id' => static::CSRF_TOKEN_ID,
            'csrf_token_key' => static::CSRF_TOKEN_KEY
        ]);
    }

    /**
     * @Route("/complete", name="complete_registration_code")
     */
    public function complete(Request $request): Response {
        $code = $this->manager->getLastRedeemedCode();

        if($code === null) {
            $this->addFlash('error', 'register.redeem.error.not_found');
            return $this->redirectToRoute('redeem_registration_code');
        }

        if($code->getRedeemingUser() !== null) {
            $this->addFlash('error', 'register.redeem.error.already_redeemed');
            return $this->redirectToRoute('redeem_registration_code');
        }

        $user = (new User())
            ->setUsername($code->getUsername())
            ->setFirstname($code->getFirstname())
            ->setLastname($code->getLastname())
            ->setEmail($code->getEmail());

        $form = $this->createForm(UserProfileCompleteType::class, $user, [
            'username_suffix' => $code->getUsernameSuffix(),
            'can_edit_username' => $code->getUsername() === null
        ]);
        $form->handleRequest($request);

        if($this->manager->mustComplete($code) === false || ($form->isSubmitted() && $form->isValid())) {
            try {
                $this->manager->complete($code, $user, $form->get('password')->getData());

                return $this->render('register/completed.html.twig', [
                    'confirmation_sent' => $user->getEmail() !== null
                ]);
            } catch (EmailAlreadyExistsException $e) {
                $form->get('email')->addError(new FormError($this->translator->trans('register.complete.error.email_aready_used', [], 'security')));
            } catch (EmailDomainNotAllowedException $e) {
                $form->get('email')->addError(new FormError($this->translator->trans('register.complete.error.domain_blacklisted', [], 'security')));
            }
        }

        return $this->render('register/complete.html.twig', [
            'form' => $form->createView(),
            'code' => $code
        ]);
    }

    /**
     * @Route("/confirm/{token}", name="confirm_registration_code")
     */
    public function confirm(string $token): Response {
        try {
            $this->manager->confirm($token);

            $this->addFlash('success', $this->translator->trans('register.confirmed.message', [], 'security'));
            return $this->redirectToRoute('login');
        } catch (TokenNotFoundException $e) {
            $this->addFlash('error', $this->translator->trans('register.confirmed.error.not_found', [], 'security'));
        }

        return $this->render('register/confirm_error.html.twig');
    }

    private function getCsrfTokenMessage(): string {
        return $this->translator->trans('Invalid CSRF token.', [], 'security');
    }
}