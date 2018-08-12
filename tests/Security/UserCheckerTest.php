<?php
namespace App\Tests\Security;

use App\Entity\User;
use App\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use SchoolIT\CommonBundle\Helper\DateHelper;

class UserCheckerTest extends TestCase {

    public function testGrantActiveUser() {
        $dateHelper = new DateHelper();
        $userChecker = new UserChecker($dateHelper);

        $user = (new User())
            ->setActive(true)
            ->setEnabledFrom(null)
            ->setEnabledUntil(null);

        $userChecker->checkPostAuth($user);
        $this->addToAssertionCount(1);
    }

    /**
     * @expectedException App\Security\AccountDisabledException
     */
    public function testDoNotGrantNonActiveUser() {
        $dateHelper = new DateHelper();
        $userChecker = new UserChecker($dateHelper);

        $user = (new User())
            ->setActive(false)
            ->setEnabledFrom(null)
            ->setEnabledUntil(null);

        $userChecker->checkPostAuth($user);
    }

    /**
     * Test whether a user is concidered non active as the enabled window
     * starts in the future
     *
     * @expectedException App\Security\AccountDisabledException
     */
    public function testDoNotGrandActiveUserWithTimeWindowFutureEnabled() {
        $dateHelper = new DateHelper(new \DateTime('2018-08-01'));
        $userChecker = new UserChecker($dateHelper);

        $user = (new User())
            ->setActive(true)
            ->setEnabledFrom(new \DateTime('2018-08-02'))
            ->setEnabledUntil(null);

        $userChecker->checkPostAuth($user);
    }

    /**
     * Test whether a user is concidered non active as the enabled window
     * starts in the future
     *
     * @expectedException App\Security\AccountDisabledException
     */
    public function testDoNotGrandActiveUserWithTimeWindowPastEnabled() {
        $dateHelper = new DateHelper(new \DateTime('2018-08-01'));
        $userChecker = new UserChecker($dateHelper);

        $user = (new User())
            ->setActive(true)
            ->setEnabledFrom(null)
            ->setEnabledUntil(new \DateTime('2018-07-31'));

        $userChecker->checkPostAuth($user);
    }

    public function testGrandActiveUserWithTimeWindow() {
        $dateHelper = new DateHelper(new \DateTime('2018-08-01'));
        $userChecker = new UserChecker($dateHelper);

        $user = (new User())
            ->setActive(true)
            ->setEnabledFrom(new \DateTime('2018-07-01'))
            ->setEnabledUntil(new \DateTime('2018-09-30'));

        $userChecker->checkPostAuth($user);

        $this->addToAssertionCount(1);
    }
}