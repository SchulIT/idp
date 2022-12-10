<?php

namespace App\Security\ForgotPassword;

use DateTime;
use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Repository\PasswordResetTokenRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordManager {
    private const DefaultTokenLifetime = '24 hours';

    public function __construct(private DateHelper $dateHelper, private UserPasswordHasherInterface $passwordHasher, private UserRepositoryInterface $userRepository, private PasswordResetTokenRepositoryInterface $passwordResetTokenRepository, private $tokenLifetime = self::DefaultTokenLifetime)
    {
    }

    /**
     * @return PasswordResetToken
     */
    public function createPasswordToken(User $user) {
        $token = $this->passwordResetTokenRepository
            ->findOneByUser($user);

        if($token !== null && $this->dateHelper->getNow() < $token->getExpiresAt()) {
            // Token is still valid -> do not create a new one
            return $token;
        }

        $expiresAt = (new DateTime())
            ->modify(sprintf('+%s', $this->tokenLifetime));

        $token = (new PasswordResetToken())
            ->setUser($user)
            ->setToken(bin2hex(random_bytes(32)))
            ->setExpiresAt($expiresAt);

        $this->passwordResetTokenRepository->persist($token);

        return $token;
    }

    /**
     * @return PasswordResetToken|null
     */
    public function getPasswordToken(string $token) {
        return $this->passwordResetTokenRepository
            ->findOneByToken($token);
    }

    public function setPassword(PasswordResetToken $token, string $password) {
        $user = $token->getUser();

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $this->userRepository->persist($user);

        $this->removePasswordToken($token);
    }

    public function removePasswordToken(PasswordResetToken $token) {
        $this->passwordResetTokenRepository->remove($token);
    }
}