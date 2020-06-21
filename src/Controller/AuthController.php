<?php

namespace App\Controller;

use Anyx\LoginGateBundle\Service\BruteForceChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController {
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authUtils, BruteForceChecker $bruteForceChecker, Request $request) {
        if($bruteForceChecker->canLogin($request) === false) {
            return $this->render('auth/too_many_attempts.html.twig', [

            ]);
        }

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

    /**
     * @Route("/logout/success", name="logout_success")
     */
    public function logoutSuccess() {
        return $this->render('auth/logout.html.twig', [
            'error' => null
        ]);
    }
}