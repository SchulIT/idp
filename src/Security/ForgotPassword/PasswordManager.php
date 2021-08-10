<?php

namespace App\Security\ForgotPassword;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Repository\PasswordResetTokenRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordManager {
    private const DefaultTokenLifetime = '24 hours';

    private $dateHelper;
    private $passwordHasher;
    private $userRepository;
    private $passwordResetTokenRepository;
    private $tokenLifetime;

    public function __construct(DateHelper $dateHelper, UserPasswordHasherInterface $passwordHasher, UserRepositoryInterface $userRepository, PasswordResetTokenRepositoryInterface $passwordResetTokenRepository, $tokenLifetime = self::DefaultTokenLifetime) {
        $this->dateHelper = $dateHelper;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
        $this->passwordResetTokenRepository = $passwordResetTokenRepository;
        $this->tokenLifetime = $tokenLifetime;
    }

    /**
     * @param User $user
     * @return PasswordResetToken
     */
    public function createPasswordToken(User $user) {
        $token = $this->passwordResetTokenRepository
            ->findOneByUser($user);

        if($token !== null && $this->dateHelper->getNow() < $token->getExpiresAt()) {
            // Token is still valid -> do not create a new one
            return $token;
        }

        $expiresAt = (new \DateTime())
            ->modify(sprintf('+%s', $this->tokenLifetime));

        $token = (new PasswordResetToken())
            ->setUser($user)
            ->setToken(bin2hex(random_bytes(32)))
            ->setExpiresAt($expiresAt);

        $this->passwordResetTokenRepository->persist($token);

        return $token;
    }

    /**
     * @param string $token
     * @return PasswordResetToken|null
     */
    public function getPasswordToken(string $token) {
        return $this->passwordResetTokenRepository
            ->findOneByToken($token);
    }

    /**
     * @param PasswordResetToken $token
     * @param string $password
     */
    public function setPassword(PasswordResetToken $token, string $password) {
        $user = $token->getUser();

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $this->userRepository->persist($user);

        $this->removePasswordToken($token);
    }

    /**
     * @param PasswordResetToken $token
     */
    public function removePasswordToken(PasswordResetToken $token) {
        $this->passwordResetTokenRepository->remove($token);
    }
}