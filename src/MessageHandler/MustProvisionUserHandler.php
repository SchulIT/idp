<?php

namespace App\MessageHandler;

use App\Message\MustProvisionUser;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MustProvisionUserHandler implements MessageHandlerInterface {

    private UserRepositoryInterface $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserRepositoryInterface $userRepository, UserPasswordHasherInterface $passwordHasher) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function __invoke(MustProvisionUser $message) {
        $user = $this->userRepository->findOneById($message->getUserId());

        if($user !== null) {
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $user->getPassword())
            );

            $user->setIsProvisioned(true);
            $this->userRepository->persist($user);
        }
        // if user was not found -> discard message
    }
}