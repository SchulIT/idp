<?php

namespace App\Oidc\EventSubscriber;

use Ajgarlag\Bundle\OpenIDConnectProviderBundle\Event\UserClaimsResolveEvent;
use App\Entity\OidcServiceProvider;
use App\Repository\UserRepositoryInterface;
use App\Service\Attribute\EffectiveAttributeResolver;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class UserClaimsResolveEventSubscriber implements EventSubscriberInterface {

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EffectiveAttributeResolver $attributeValueResolver
    ) {

    }

    public function onUserClaimsResolve(UserClaimsResolveEvent $event): void {
        $user = $this->userRepository->findOneByUsername($event->getIdentifier());

        $claims = [
            // profile
            'family_name' => $user->getLastname() ?? '',
            'given_name' => $user->getFirstname() ?? '',
            'nickname' => $user->getUsername(),
            'preferred_username' => $user->getUsername(),
            'updated_at' => $user->getUpdatedAt()->format(DATE_ATOM),

            // email
            'email' => $user->getEmail() ?? '',
            'email_verified' => !empty($user->getEmail()),

            // schulit
            'schulit:uuid' => $user->getUuidString(),
            'schulit:type' => $user->getTypeString(),
            'schulit:grade' => $user->getgrade() ?? '',
            'schulit:external_id' => $user->getExternalId() ?? '',
            'schulit:affiliation' => $user->getType()->getEduPerson()
        ];

        // groups
        $groups = [ $user->getType()->getName() ];

        foreach($user->getUserRoles() as $role) {
            $groups[] = $role->getName();
        }

        $claims['groups'] = $groups;

        foreach($this->attributeValueResolver->getDetailedAttributeValuesForUser($user) as $attribute) {
            foreach($attribute->attribute->getServices() as $service) {
                if(!$service instanceof OidcServiceProvider || empty($attribute->attribute->getOidcClaimName())) {
                    continue;
                }

                $key = sprintf('%s:%s', $service->getClient()->getScopeName(), $attribute->attribute->getOidcClaimName());
                $key = mb_strtolower($key);

                $claims[$key] = $attribute->value;
            }
        }

        $event->setClaims($claims);
    }

    #[Override]
    public static function getSubscribedEvents(): array {
        return [
            UserClaimsResolveEvent::class => 'onUserClaimsResolve',
        ];
    }
}