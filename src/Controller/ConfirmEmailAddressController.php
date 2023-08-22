<?php

namespace App\Controller;

use App\Security\EmailConfirmation\ConfirmationManager;
use App\Security\EmailConfirmation\EmailAddressAlreadyInUseException;
use App\Security\EmailConfirmation\TokenNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmEmailAddressController extends AbstractController {
    #[Route(path: '/confirm/{token}', name: 'confirm_email')]
    public function confirmEmailAddress($token, ConfirmationManager $confirmationManager): Response {
        if(empty($token)) {
            return $this->redirectToRoute('dashboard');
        }

        try {
            $confirmation = $confirmationManager->getConfirmation($token);
            $confirmationManager->confirm($confirmation);
        } catch (TokenNotFoundException) {
            return $this->render('confirmation/email.html.twig', [
                'error' => 'email_confirmation.error.not_found'
            ]);
        } catch(EmailAddressAlreadyInUseException) {
            return $this->render('confirmation/email.html.twig', [
                'error' => 'email_confirmation.error.email_address_already_in_use'
            ]);
        }

        return $this->render('confirmation/email.html.twig', [
            'error' => false
        ]);
    }
}