<?php

namespace App\MessageHandler;

use App\Message\MustProvisionUser;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MustProvisionUserHandler implements MessageHandlerInterface {

    private $userRepository;
    private $passwordEncoder;

    public function __construct(UserRepositoryInterface $userRepository, UserPasswordEncoderInterface $passwordEncoder) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function __invoke(MustProvisionUser $message) {
        $user = $this->userRepository->findOneById($message->getUserId());

        if($user !== null) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $user->getPassword())
            );

            $user->setIsProvisioned(true);
            $this->userRepository->persist($user);
        }
        // if user was not found -> discard message
    }
}