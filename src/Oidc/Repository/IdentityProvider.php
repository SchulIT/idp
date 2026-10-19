<?php

namespace App\Oidc\Repository;

use Ajgarlag\Bundle\OpenIDConnectProviderBundle\Event\UserClaimsResolveEvent;
use Ajgarlag\Bundle\OpenIDConnectProviderBundle\Model\Identity;
use App\Repository\UserRepositoryInterface;
use OpenIDConnectServer\Repositories\IdentityProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class IdentityProvider implements IdentityProviderInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @param non-empty-string $identifier
     *
     * @return Identity
     */
    public function getUserEntityByIdentifier($identifier): Identity {
        $userEntity = $this->userRepository->findOneByUsername($identifier);

        if($userEntity === null) {
            return new Identity();
        }

        $user = new Identity();
        $user->setIdentifier($userEntity->getUuidString());

        /** @var UserClaimsResolveEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new UserClaimsResolveEvent($identifier)
        );

        $user->setClaims($event->getClaims());

        return $user;
    }
}
