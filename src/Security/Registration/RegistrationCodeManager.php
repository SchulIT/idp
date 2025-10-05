<?php

declare(strict_types=1);

namespace App\Security\Registration;

use App\Entity\RegistrationCode;
use App\Entity\User;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use App\Security\EmailConfirmation\ConfirmationManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationCodeManager {

    public function __construct(private readonly RegistrationCodeRepositoryInterface $codeRepository, private readonly UserRepositoryInterface $userRepository, private readonly UserPasswordHasherInterface $passwordHasher, private readonly UserTypeRepositoryInterface $typeRepository, private readonly ConfirmationManager $confirmationManager)
    {
    }

    public function getTemplateUser(): User {
        $type = $this->typeRepository->findOneByAlias('parent');
        $user = new User();
        $user->setType($type);

        return $user;
    }

    public function isRedeemed(RegistrationCode $code): bool {
        return $code->getRedeemingUser() instanceof User;
    }

    /**
     * @throws CodeAlreadyRedeemedException
     * @return bool Whether an email confirmation was sent.
     */
    public function complete(RegistrationCode $code, User $user, string $password): bool {
        if($this->isRedeemed($code)) {
            throw new CodeAlreadyRedeemedException();
        }

        $type = $this->typeRepository->findOneByAlias('parent');
        $user
            ->setType($type)
            ->setPassword($this->passwordHasher->hashPassword($user, $password));

        $user->addLinkedStudent($code->getStudent());
        $code->setRedeemingUser($user);

        $this->userRepository->persist($user);
        $this->codeRepository->persist($code);

        if($user->getEmail() !== null) {
            $this->confirmationManager->newConfirmation($user, $user->getEmail());
            $user->setEmail(null);

            $this->userRepository->persist($user);
            return true;
        }

        return false;
    }
}
