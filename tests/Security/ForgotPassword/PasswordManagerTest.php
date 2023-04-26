<?php

namespace App\Tests\Security\ForgotPassword;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Repository\PasswordResetTokenRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\ForgotPassword\PasswordManager;
use PHPUnit\Framework\TestCase;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordManagerTest extends TestCase {
    public function testCreateTokenIfNotPresent() {
        $dateHelper = new DateHelper();
        $userPasswordEncoder = $this->createMock(UserPasswordHasherInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordResetRepository = $this->createMock(PasswordResetTokenRepositoryInterface::class);
        $passwordResetRepository
            ->method('findOneByUser')
            ->willReturn(null);

        $manager = new PasswordManager($dateHelper, $userPasswordEncoder, $userRepository, $passwordResetRepository);

        $user = new User();

        $token = $manager->createPasswordToken($user);

        $this->assertNotNull($token);
        $this->assertEquals($user, $token->getUser());
    }

    public function testCreateTokenIfPresentAndNotExpired() {
        $existingToken = (new PasswordResetToken())
            ->setToken('token')
            ->setExpiresAt(new \DateTime('2019-02-14 11:00'));

        $dateHelper = $this->createMock(DateHelper::class);
        $dateHelper
            ->method('getNow')
            ->willReturn(new \DateTime('2019-02-14 10:00'));

        $userPasswordEncoder = $this->createMock(UserPasswordHasherInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordResetRepository = $this->createMock(PasswordResetTokenRepositoryInterface::class);
        $passwordResetRepository
            ->method('findOneByUser')
            ->willReturn($existingToken);

        $manager = new PasswordManager($dateHelper, $userPasswordEncoder, $userRepository, $passwordResetRepository, '2 hours');

        $user = new User();

        $token = $manager->createPasswordToken($user);

        $this->assertNotNull($token);
        $this->assertEquals($existingToken, $token);
    }

    public function testCreateTokenIfPresentAndExpired() {
        $existingToken = (new PasswordResetToken())
            ->setToken('token')
            ->setExpiresAt(new \DateTime('2019-02-14 09:59'));

        $dateHelper = $this->createMock(DateHelper::class);
        $dateHelper
            ->method('getNow')
            ->willReturn(new \DateTime('2019-02-14 10:00'));

        $userPasswordEncoder = $this->createMock(UserPasswordHasherInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordResetRepository = $this->createMock(PasswordResetTokenRepositoryInterface::class);
        $passwordResetRepository
            ->method('findOneByUser')
            ->willReturn($existingToken);

        $manager = new PasswordManager($dateHelper, $userPasswordEncoder, $userRepository, $passwordResetRepository, '2 hours');

        $user = new User();

        $token = $manager->createPasswordToken($user);

        $this->assertNotNull($token);
        $this->assertEquals($user, $token->getUser());
        $this->assertNotEquals($existingToken, $token);
    }

    public function testSetPassword() {
        $user = (new User())
            ->setPassword('old-password');
        $token = (new PasswordResetToken())
            ->setToken('token')
            ->setUser($user);

        $dateHelper = new DateHelper();
        $userPasswordEncoder = $this->createMock(UserPasswordHasherInterface::class);
        $userPasswordEncoder
            ->method('hashPassword')
            ->willReturn('new-encoded-password');
        $userPasswordEncoder
            ->expects($this->once())
            ->method('hashPassword')
            ->with($this->equalTo($user), $this->equalTo('new-password'));
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordResetRepository = $this->createMock(PasswordResetTokenRepositoryInterface::class);
        $passwordResetRepository
            ->method('findOneByUser')
            ->willReturn($token);

        $manager = new PasswordManager($dateHelper, $userPasswordEncoder, $userRepository, $passwordResetRepository);

        $manager->setPassword($token, 'new-password');
        $this->assertEquals('new-encoded-password', $user->getPassword());
    }
}