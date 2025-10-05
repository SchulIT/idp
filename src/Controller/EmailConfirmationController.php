<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\EmailConfirmation;
use App\Security\EmailConfirmation\ConfirmationManager;
use App\Security\EmailConfirmation\EmailAddressAlreadyInUseException;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmailConfirmationController extends AbstractController
{
    private const string CsrfTokenId = 'confirm_email';
    #[Route('/users/confirmations/{token}', name: 'show_email_confirmation')]
    public function index(#[MapEntity(mapping: ['token' => 'token'])] EmailConfirmation $confirmation): Response {
        return $this->render('users/confirmation.html.twig', [
            'confirmation' => $confirmation,
            'tokenId' => self::CsrfTokenId
        ]);
    }
    #[Route('/users/confirmations/{token}/action', name: 'perform_email_confirmation_action', methods: ['POST'])]
    public function action(#[MapEntity(mapping: ['token' => 'token'])] \App\Entity\EmailConfirmation $confirmation, Request $request, ConfirmationManager $confirmationManager): Response {
        if(!$this->isCsrfTokenValid(self::CsrfTokenId, $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'Csrf token invalid.');
            return $this->redirectToRoute('show_email_confirmation', [
                'token' => $confirmation->getToken()
            ]);
        }

        return match ($request->request->get('action')) {
            'confirm' => $this->confirm($confirmation, $confirmationManager),
            'send' => $this->sendConfirmation($confirmation, $confirmationManager),
            default => $this->redirectToRoute('show_email_confirmation', [
                'token' => $confirmation->getToken()
            ]),
        };

    }
    private function confirm(#[MapEntity(mapping: ['token' => 'token'])] EmailConfirmation $confirmation, ConfirmationManager $confirmationManager): Response {
        try {
            $confirmationManager->confirm($confirmation);
            $this->addFlash('success', 'user.email_confirmation.confirm.success');
        } catch (EmailAddressAlreadyInUseException) {
            $this->addFlash('error', 'user.email_confirmation.confirm.already_in_use');

            return $this->redirectToRoute('show_email_confirmation', [
                'token' => $confirmation->getToken()
            ]);
        }

        return $this->redirectToRoute('users');
    }
    private function sendConfirmation(#[MapEntity(mapping: ['token' => 'token'])] EmailConfirmation $confirmation, ConfirmationManager $confirmationManager): Response {
        try {
            $confirmationManager->newConfirmation($confirmation->getUser(), $confirmation->getEmailAddress());
            $this->addFlash('success', 'user.email_confirmation.confirm.success');
        } catch(Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('show_email_confirmation', [
                'token' => $confirmation->getToken()
            ]);
        }

        return $this->redirectToRoute('users');
    }
}
