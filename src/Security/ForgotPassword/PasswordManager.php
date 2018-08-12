<?php

namespace App\Security\ForgotPassword;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordManager {
    private const DefaultTokenLifetime = '2 hours';

    private $dateHelper;
    private $passwordEncoder;
    private $em;
    private $tokenLifetime;

    public function __construct(DateHelper $dateHelper, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, $tokenLifetime = self::DefaultTokenLifetime) {
        $this->dateHelper = $dateHelper;
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $entityManager;
        $this->tokenLifetime = $tokenLifetime;
    }

    /**
     * @param User $user
     * @return PasswordResetToken
     * @throws \Exception
     */
    public function createPasswordToken(User $user) {
        /** @var PasswordResetToken $token */
        $token = $this->em
            ->getRepository(PasswordResetToken::class)
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

        $this->em->persist($token);
        $this->em->flush();

        return $token;
    }

    /**
     * @param string $token
     * @return PasswordResetToken|null
     */
    public function getPasswordToken(string $token) {
        return $this->em
            ->getRepository(PasswordResetToken::class)
            ->findOneByToken($token);
    }

    /**
     * @param PasswordResetToken $token
     * @param string $password
     */
    public function setPassword(PasswordResetToken $token, string $password) {
        $user = $token->getUser();

        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        $this->em->persist($user);
        $this->em->flush();

        $this->removePasswordToken($token);
    }

    /**
     * @param PasswordResetToken $token
     */
    public function removePasswordToken(PasswordResetToken $token) {
        $this->em->remove($token);
        $this->em->flush();
    }
}