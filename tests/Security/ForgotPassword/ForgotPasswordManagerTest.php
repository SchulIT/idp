<?php

namespace App\Tests\Security\ForgotPassword;

use App\Entity\ActiveDirectoryUser;
use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Repository\PasswordResetTokenRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\ForgotPassword\ForgotPasswordManager;
use App\Security\ForgotPassword\TooManyRequestsException;
use App\Security\ForgotPassword\UserCannotResetPasswordException;
use DateTime;
use PHPUnit\Framework\TestCase;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ForgotPasswordManagerTest extends TestCase {
    public function testActiveDirectoryUsersCannotRequestNewPassword() {
        $manager = new ForgotPasswordManager(
            $this->createMock(PasswordResetTokenRepositoryInterface::class),
            $this->createMock(Environment::class),
            $this->createMock(MailerInterface::class),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(DateHelper::class),
            $this->createMock(UserPasswordHasherInterface::class),
            $this->createMock(UserRepositoryInterface::class)
        );

        $this->expectException(UserCannotResetPasswordException::class);
        $manager->createPasswordResetRequest(new ActiveDirectoryUser(), 'foo@bla.com');
    }

    public function testUsersWithoutEmailAddressCannotRequestNewPassword() {
        $manager = new ForgotPasswordManager(
            $this->createMock(PasswordResetTokenRepositoryInterface::class),
            $this->createMock(Environment::class),
            $this->createMock(MailerInterface::class),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(DateHelper::class),
            $this->createMock(UserPasswordHasherInterface::class),
            $this->createMock(UserRepositoryInterface::class)
        );

        $this->expectException(UserCannotResetPasswordException::class);
        $manager->createPasswordResetRequest(new User(), null);
    }

    public function testTooManyRequests() {
        $dateHelper = new DateHelper();
        $dateHelper->setToday(new DateTime('2023-09-02 00:00:00'));

        $expiredToken = (new PasswordResetToken())
            ->setExpiresAt(
                $dateHelper->getNow()
                    ->modify(sprintf('-%d minutes', ForgotPasswordManager::LifeTimeInMinutes + 10))
            );

        $repository = $this->createMock(PasswordResetTokenRepositoryInterface::class);
        $repository
            ->method('findMostRecentNonExpired')
            ->willReturn($expiredToken);

        $manager = new ForgotPasswordManager(
            $repository,
            $this->createMock(Environment::class),
            $this->createMock(MailerInterface::class),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(DateHelper::class),
            $this->createMock(UserPasswordHasherInterface::class),
            $this->createMock(UserRepositoryInterface::class)
        );

        $this->expectException(TooManyRequestsException::class);
        $manager->createPasswordResetRequest(new User(), 'foo@example.com');
    }

    public function testGarbageCollection() {
        $repository = $this->createMock(PasswordResetTokenRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('removeExpired');

        $manager = new ForgotPasswordManager(
            $repository,
            $this->createMock(Environment::class),
            $this->createMock(MailerInterface::class),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(DateHelper::class),
            $this->createMock(UserPasswordHasherInterface::class),
            $this->createMock(UserRepositoryInterface::class)
        );

        $manager->garbageCollect();
    }

    public function testCreateNewToken() {
        $dateHelper = new DateHelper();
        $dateHelper->setToday(new DateTime('2023-09-02 00:00:00'));

        $repository = $this->createMock(PasswordResetTokenRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('persist');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer
            ->expects($this->once())
            ->method('send');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator
            ->method('generate')
            ->willReturn('https://localhost/forgot_pw/abc');

        $twig = $this->createMock(Environment::class);
        $twig
            ->method('render')
            ->willReturn('<html></html>');

        $manager = new ForgotPasswordManager(
            $repository,
            $twig,
            $mailer,
            $urlGenerator,
            new IdentityTranslator(),
            $dateHelper,
            $this->createMock(UserPasswordHasherInterface::class),
            $this->createMock(UserRepositoryInterface::class)
        );

        $user = new User();
        $user->setUsername('foo@example.com');
        $token = $manager->createPasswordResetRequest($user, 'foo@example.com');

        $this->assertEquals($user, $token->getUser(), 'Token should hold the user for which the request was made');
        $this->assertGreaterThan($dateHelper->getNow(), $token->getExpiresAt(), 'Token should have an expiry date in the future');
        $this->assertLessThanOrEqual($dateHelper->getNow()->modify(sprintf('+%d minutes', ForgotPasswordManager::LifeTimeInMinutes + 10 /* add a little extra time so this test should not fail due to a time error */)), $token->getExpiresAt(), 'Token should not expire before the lifetime');
    }
}