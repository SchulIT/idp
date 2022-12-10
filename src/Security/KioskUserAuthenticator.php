<?php

namespace App\Security;

use App\Repository\KioskUserRepositoryInterface;
use App\Security\Badge\ClientIpAddressBadge;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class KioskUserAuthenticator extends AbstractLoginFormAuthenticator {

    private KioskUserRepositoryInterface $repository;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(KioskUserRepositoryInterface $repository, UrlGeneratorInterface $urlGenerator) {
        $this->repository = $repository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): bool {
        return $request->query->has('token');
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $firewallName): ?Response {
        return new RedirectResponse($this->urlGenerator->generate('dashboard'));
    }

    /**
     * @inheritDoc
     */
    protected function getLoginUrl(Request $request): string {
        return $this->urlGenerator->generate('login');
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport {
        $token = $request->query->get('token');

        $user = $this->repository->findOneByToken($token);

        if($user === null) {
            throw new UserNotFoundException();
        }

        return new SelfValidatingPassport(
            new UserBadge($user->getUser()->getUserIdentifier()),
            [
                new ClientIpAddressBadge(explode(',', $user->getIpAddresses()))
            ]
        );
    }
}