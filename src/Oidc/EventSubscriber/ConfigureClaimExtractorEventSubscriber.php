<?php

namespace App\Oidc\EventSubscriber;

use App\Entity\OidcServiceProvider;
use App\Repository\ServiceAttributeRepositoryInterface;
use App\Repository\ServiceProviderRepositoryInterface;
use OpenIDConnectServer\ClaimExtractor;
use OpenIDConnectServer\Entities\ClaimSetEntity;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

readonly class ConfigureClaimExtractorEventSubscriber implements EventSubscriberInterface {

    public function __construct(
        private ServiceProviderRepositoryInterface $providerRepository,
        private ServiceAttributeRepositoryInterface $attributeRepository,
        #[Autowire(service: 'ajgarlag.openid_connect_provider.openid_connect.claim_extractor')] private ClaimExtractor $claimExtractor,
    ) {

    }

    public function onRequest(RequestEvent $event): void {
        // Add OIDC
        $this->claimExtractor->addClaimSet(
            new ClaimSetEntity('openid', [
                'sub',
                'iss'
            ])
        );

        // groups
        $this->claimExtractor->addClaimSet(
            new ClaimSetEntity(
                'groups', [
                    'groups'
                ]
            )
        );

        // Add SchulIT claimset
        $this->claimExtractor->addClaimSet(
            new ClaimSetEntity('schulit', [
                'schulit:uuid',
                'schulit:affiliation',
                'schulit:type',
                'schulit:grade',
                'schulit:external_id'
            ])
        );

        // Add OIDC service providers as claimset
        foreach($this->providerRepository->findAll() as $provider) {
            if(!$provider instanceof OidcServiceProvider) {
                continue;
            }

            $claims = [ ];

            foreach($this->attributeRepository->getAttributesForServiceProvider($provider) as $attribute) {
                if(empty($attribute->getOidcClaimName())) {
                    continue;
                }

                $claim = sprintf('%s:%s', $provider->getClient()->getScopeName(), $attribute->getOidcClaimName());
                $claim = mb_strtolower($claim);

                $claims[] = $claim;
            }

            if(count($claims) > 0) {
                $this->claimExtractor->addClaimSet(
                    new ClaimSetEntity(mb_strtolower($provider->getClient()->getScopeName()), $claims)
                );
            }
        }
    }

    #[Override]
    public static function getSubscribedEvents(): array {
        return [
            RequestEvent::class => 'onRequest',
        ];
    }
}