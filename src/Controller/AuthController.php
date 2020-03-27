<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController {
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authUtils) {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('auth/auth.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'supportsForgotPassword' => true,
            'supportsRememberMe' => true
        ]);
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheck() {

    }
}