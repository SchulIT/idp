<?php

declare(strict_types=1);

namespace App\Security\EventListener;

use App\Entity\ActiveDirectoryUser;
use App\Repository\UserRepositoryInterface;
use App\Security\Badge\CachePasswordBadge;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

readonly class CachePasswordListener implements EventSubscriberInterface {

    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void {
        $passport = $event->getPassport();

        if(!$passport->hasBadge(CachePasswordBadge::class)) {
            return;
        }

        /** @var CachePasswordBadge $badge */
        $badge = $passport->getBadge(CachePasswordBadge::class);
        $plaintextPassword = $badge->getPassword();

        $user = $passport->getUser();

        if(!$user instanceof ActiveDirectoryUser) {
            return;
        }

        $hasher = $badge->getHasher();

        /*
         * Check if password was changed and only update if password has indeed changed. This is needed so Symfony's
         * ContextListener does not detect a change in the user entity which ensures users stay logged in using the
         * "remember me"-functionality.
         */
        if($hasher->isPasswordValid($user, $badge->getPassword())) {
            return;
        }

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
