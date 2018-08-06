<?php

namespace App\Security;

use App\Security\Authentication\Token\ApplicationToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class ApplicationAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface {

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {
        if(!$userProvider instanceof ApplicationUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApplicationUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $apiKey = $token->getCredentials();
        $application = $userProvider->loadUserByApiKey($apiKey);

        if($application === null) {
            throw new AuthenticationException();
        }

        return new ApplicationToken(
            $apiKey,
            $application,
            $application->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof ApplicationToken;
    }

    public function createToken(Request $request, $providerKey) {
        $apiKey = $request->headers->get('x-token');

        if($apiKey === null) {
            throw new BadCredentialsException();
        }

        return new ApplicationToken($apiKey);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        return new JsonResponse([
            'success' => false,
            'message' => $exception->getMessage(),
            'type' => get_class($exception)
        ], 401);
    }
}