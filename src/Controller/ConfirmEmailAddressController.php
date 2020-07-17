<?php

namespace App\Controller;

use App\Security\EmailConfirmation\ConfirmationManager;
use App\Security\EmailConfirmation\TokenNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmEmailAddressController extends AbstractController {
    /**
     * @Route("/confirm/{token}", name="confirm_email")
     */
    public function confirmEmailAddress($token, ConfirmationManager $confirmationManager) {
        if(empty($token)) {
            return $this->redirectToRoute('dashboard');
        }

        try {
            $confirmationManager->confirm($token);
        } catch (TokenNotFoundException $e) {
            return $this->render('confirmation/email.html.twig', [
                'error' => true
            ]);
        }

        return $this->render('confirmation/email.html.twig', [
            'error' => false
        ]);
    }
}