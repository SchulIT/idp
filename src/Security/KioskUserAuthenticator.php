<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\KioskUser;
use App\Repository\KioskUserRepositoryInterface;
use App\Security\Badge\ClientIpAddressBadge;
use Override;
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

    public function __construct(private readonly KioskUserRepositoryInterface $repository, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * @inheritDoc
     */
    #[Override]
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

        if(!$user instanceof KioskUser) {
            throw new UserNotFoundException();
        }

        return new SelfValidatingPassport(
            new UserBadge($user->getUser()->getUserIdentifier()),
            [
                new ClientIpAddressBadge(explode(',', (string) $user->getIpAddresses()))
            ]
        );
    }
}
