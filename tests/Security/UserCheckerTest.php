<?php
namespace App\Tests\Security;

use App\Entity\User;
use App\Security\AccountDisabledException;
use App\Security\EmailConfirmation\ConfirmationManager;
use App\Security\UserChecker;
use DateTime;
use PHPUnit\Framework\TestCase;
use SchulIT\CommonBundle\Helper\DateHelper;

class UserCheckerTest extends TestCase {

    public function testGrantActiveUser() {
        $dateHelper = new DateHelper();
        $userChecker = new UserChecker($dateHelper);

        $user = (new User())
            ->setIsActive(true)
            ->setEnabledFrom(null)
            ->setEnabledUntil(null);

        $userChecker->checkPostAuth($user);
        $this->addToAssertionCount(1);
    }

    public function testDoNotGrantNonActiveUser() {
        $this->expectException(AccountDisabledException::class);
        $dateHelper = new DateHelper();
        $userChecker = new UserChecker($dateHelper);

        $user = (new User())
            ->setIsActive(false)
            ->setEnabledFrom(null)
            ->setEnabledUntil(null);

        $userChecker->checkPostAuth($user);
    }

    /**
     * Test whether a user is concidered non-active as the enabled window
     * starts in the future
     *
     *
     */
    public function testDoNotGrandActiveUserWithTimeWindowFutureEnabled() {
        $this->expectException(AccountDisabledException::class);
        $dateHelper = new DateHelper(new DateTime('2018-08-01'));
        $userChecker = new UserChecker($dateHelper);

        $user = (new User())
            ->setIsActive(true)
            ->setEnabledFrom(new DateTime('2018-08-02'))
            ->setEnabledUntil(null);

        $userChecker->checkPostAuth($user);
    }

    /**
     * Test whether a user is concidered non-active as the enabled window
     * starts in the future
     *
     *
     */
    public function testDoNotGrandActiveUserWithTimeWindowPastEnabled() {
        $this->expectException(AccountDisabledException::class);
        $dateHelper = new DateHelper(new DateTime('2018-08-01'));
        $userChecker = new UserChecker($dateHelper);

        $user = (new User())
            ->setIsActive(true)
            ->setEnabledFrom(null)
            ->setEnabledUntil(new DateTime('2018-07-31'));

        $userChecker->checkPostAuth($user);
    }

    public function testGrandActiveUserWithTimeWindow() {
        $dateHelper = new DateHelper(new DateTime('2018-08-01'));
        $userChecker = new UserChecker($dateHelper);

        $user = (new User())
            ->setIsActive(true)
            ->setEnabledFrom(new DateTime('2018-07-01'))
            ->setEnabledUntil(new DateTime('2018-09-30'));

        $userChecker->checkPostAuth($user);

        $this->addToAssertionCount(1);
    }
}