<?php

namespace App\Security\EventListener;

use App\Entity\ActiveDirectoryUser;
use App\Repository\UserRepositoryInterface;
use App\Security\Badge\CachePasswordBadge;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class CachePasswordListener implements EventSubscriberInterface {

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void {
        $passport = $event->getPassport();

        if(!$passport instanceof UserPassportInterface || !$passport->hasBadge(CachePasswordBadge::class)) {
            return;
        }

        /** @var CachePasswordBadge $badge */
        $badge = $passport->getBadge(CachePasswordBadge::class);
        $plaintextPassword = $badge->getPassword();

        $user = $passport->getUser();

        if(!$user instanceof ActiveDirectoryUser) {
            return;
        }

        if($badge->isResolved()) {
            return;
        }

        $hasher = $badge->getHasher();

        $user->setPassword($hasher->hashPassword($user, $plaintextPassword));
        $this->userRepository->persist($user);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess'
        ];
    }
}