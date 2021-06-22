<?php

namespace App\Security\Registration;

use App\Entity\RegistrationCode;
use App\Entity\User;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use App\Security\EmailConfirmation\ConfirmationManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationCodeManager {

    private $codeRepository;
    private $userRepository;
    private $passwordHasher;
    private $typeRepository;
    private $confirmationManager;

    public function __construct(RegistrationCodeRepositoryInterface $codeRepository,
                                UserRepositoryInterface $userRepository, UserPasswordHasherInterface $passwordHasher,
                                UserTypeRepositoryInterface $typeRepository, ConfirmationManager $confirmationManager) {
        $this->codeRepository = $codeRepository;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->typeRepository = $typeRepository;
        $this->confirmationManager = $confirmationManager;
    }

    public function getTemplateUser(): User {
        $type = $this->typeRepository->findOneByAlias('parent');
        $user = new User();
        $user->setType($type);

        return $user;
    }

    public function isRedeemed(RegistrationCode $code): bool {
        return $code->getRedeemingUser() !== null;
    }

    /**
     * @param RegistrationCode $code
     * @param User $user
     * @param string $password
     * @throws CodeAlreadyRedeemedException
     */
    public function complete(RegistrationCode $code, User $user, string $password): void {
        if($this->isRedeemed($code)) {
            throw new CodeAlreadyRedeemedException();
        }

        $type = $this->typeRepository->findOneByAlias('parent');
        $user
            ->setType($type)
            ->setIsEmailConfirmationPending($user->getEmail() !== null)
            ->setPassword($this->passwordHasher->encodePassword($user, $password));

        $user->addLinkedStudent($code->getStudent());
        $code->setRedeemingUser($user);

        $this->userRepository->persist($user);
        $this->codeRepository->persist($code);

        if($user->getEmail() !== null) {
            $this->confirmationManager->newConfirmation($user, $user->getEmail());
            $user->setIsEmailConfirmationPending(true);

            $this->userRepository->persist($user);
        }
    }
}