<?php

namespace App\Security;

use App\Repository\ApplicationRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApplicationAuthenticator extends AbstractAuthenticator {

    public const HEADER_KEY = 'X-Token';

    public function __construct(private readonly ApplicationRepositoryInterface $repository)
    {
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        return new JsonResponse([
            'success' => false,
            'message' => sprintf('Authentication failed: %s', $exception->getMessage())
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): bool {
        return $request->headers->has(static::HEADER_KEY);
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport {
        $token = $request->headers->get(static::HEADER_KEY);
        $application = $this->repository->findOneByApiKey($token);

        if($application === null) {
            throw new AuthenticationException('Invalid API key');
        }

        return new SelfValidatingPassport(
            new UserBadge($token, fn($token) => $this->repository->findOneByApiKey($token))
        );
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        return null;
    }
}