<?php

namespace App\Security;

use App\Repository\ServiceProviderRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ServiceProviderAuthenticator extends AbstractGuardAuthenticator {

    private const HEADER_KEY = 'X-Token';

    private $repository;

    public function __construct(ServiceProviderRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null) {
        return new JsonResponse([
            'message' => 'Authentication required',
            'success' => false
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request) {
        return $request->headers->has(static::HEADER_KEY);
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request) {
        return [
            'token' => $request->headers->get(static::HEADER_KEY)
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider) {
        $serviceProvider = $this->repository->findOneByToken($credentials['token']);

        if($serviceProvider === null) {
            throw new AuthenticationException('Invalid service provider token');
        }

        return $serviceProvider;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user) {
        // We already checked credentials within the getUser() method

        return true;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        return new JsonResponse([
            'message' => 'Authentication failed',
            'success' => false
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe() {
        return false;
    }
}