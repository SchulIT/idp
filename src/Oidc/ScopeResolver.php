<?php

namespace App\Oidc;

use App\Entity\OidcServiceProvider;
use App\Repository\ServiceProviderRepositoryInterface;
use League\Bundle\OAuth2ServerBundle\ValueObject\Scope;

readonly class ScopeResolver {
    public function __construct(
        private ServiceProviderRepositoryInterface $providerRepository
    ) { }

    /**
     * @return Scope[]
     */
    public function getScopes(): array {
        $scopes = [
            new Scope('profile'),
            new Scope('email'),
            new Scope('openid'),
            new Scope('schulit')
        ];

        foreach($this->providerRepository->findAll() as $provider) {
            if(!$provider instanceof OidcServiceProvider || $provider->getClient() === null) {
                continue;
            }

            $scopes[] = new Scope(mb_strtolower($provider->getClient()->getScopeName()));
        }

        return $scopes;
    }
}