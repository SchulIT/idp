<?php

namespace App\Security;

use App\Security\Authentication\Token\ServiceProviderToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class ServiceProviderAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface {

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

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {
        if(!$userProvider instanceof ServiceProviderUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ServiceProviderUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $token = $token->getCredentials();
        $serviceProvider = $userProvider->loadUserByToken($token);

        if($serviceProvider === null) {
            throw new AuthenticationException();
        }

        return new ServiceProviderToken(
            $token,
            $serviceProvider,
            [ ]
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof ServiceProviderToken;
    }

    public function createToken(Request $request, $providerKey) {
        $token = $request->headers->get('x-token');

        if($token === null) {
            throw new BadCredentialsException();
        }

        return new ServiceProviderToken($token);
    }
}