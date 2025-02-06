<?php

namespace App\Controller;

use App\Settings\LoginSettings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController {
    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authUtils, LoginSettings $loginSettings): Response {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('auth/auth.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'supportsForgotPassword' => true,
            'supportsRememberMe' => true,
            'loginSettings' => $loginSettings
        ]);
    }

    #[Route(path: '/login_check', name: 'login_check')]
    public function loginCheck() {

    }

    #[Route(path: '/logout/success', name: 'logout_success')]
    public function logoutSuccess(): Response {
        return $this->render('auth/logout.html.twig', [
            'error' => null
        ]);
    }
}