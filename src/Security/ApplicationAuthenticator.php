<?php

namespace App\Security;

use App\Repository\ApplicationRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApplicationAuthenticator extends AbstractGuardAuthenticator {

    public const HEADER_KEY = 'X-Token';

    private $repository;

    public function __construct(ApplicationRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        return new JsonResponse([
            'success' => false,
            'message' => sprintf('Authentication failed: %s', $exception->getMessage())
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null) {
        return new JsonResponse([
            'success' => false,
            'message' => 'Authentication required'
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
        $application = $this->repository->findOneByApiKey($credentials['token']);

        if($application === null) {
            throw new AuthenticationException('Invalid API key');
        }

        return $application;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user) {
        // Credentials already checked in getUser()
        return true;
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