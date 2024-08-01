<?php

namespace App\Security;

use App\Entity\User;
use App\Security\EmailConfirmation\ConfirmationManager;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface {

    public function __construct(private DateHelper $dateHelper)
    {
    }

    /**
     * @inheritDoc
     */
    public function checkPreAuth(UserInterface $user): void { }

    /**
     * @inheritDoc
     */
    public function checkPostAuth(UserInterface $user): void {
        if(!$user instanceof User) {
            return;
        }

        // First check: is the user account marked "active"?
        if($user->isActive() !== true) {
            throw new AccountDisabledException();
        }

        // Second check: does the user has a time window in which the account is active?
        if($user->getEnabledFrom() !== null || $user->getEnabledUntil() !== null) {
            $today = $this->dateHelper->getToday();

            if($user->getEnabledFrom() !== null && $user->getEnabledFrom() > $today) {
                throw new AccountDisabledException();
            }

            if($user->getEnabledUntil() !== null && $user->getEnabledUntil() < $today) {
                throw new AccountDisabledException();
            }
        }
    }
}