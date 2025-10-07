<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Security\EmailConfirmation\ConfirmationManager;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface {

    public function __construct(private readonly DateHelper $dateHelper)
    {
    }

    /**
     * @inheritDoc
     */
    public function checkPreAuth(UserInterface $user): void { }

    /**
     * @inheritDoc
     */
    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void {
        if(!$user instanceof User) {
            return;
        }

        // First check: is the user account marked "active"?
        if(!$user->isActive()) {
            throw new AccountDisabledException();
        }

        // Second check: does the user has a time window in which the account is active?
        if($user->getEnabledFrom() instanceof DateTime || $user->getEnabledUntil() instanceof DateTime) {
            $today = $this->dateHelper->getToday();

            if($user->getEnabledFrom() instanceof DateTime && $user->getEnabledFrom() > $today) {
                throw new AccountDisabledException();
            }

            if($user->getEnabledUntil() instanceof DateTime && $user->getEnabledUntil() < $today) {
                throw new AccountDisabledException();
            }
        }
    }
}
