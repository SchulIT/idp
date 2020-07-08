<?php

namespace App\Command;

use App\Repository\UserRepositoryInterface;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @CronJob("*\/2 * * * * ")
 */
class ProvisionUserCommand extends Command {

    private $numberOfUsers;
    private $userRepository;
    private $passwordEncoder;

    public function __construct(int $numberOfUsers, UserPasswordEncoderInterface $passwordEncoder, UserRepositoryInterface $userRepository, string $name = null) {
        parent::__construct($name);

        $this->numberOfUsers = $numberOfUsers;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function configure() {
        $this
            ->setName('app:user:provision')
            ->setDescription('Provisions the next N users');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        $users = $this->userRepository->findNextNonProvisionedUsers($this->numberOfUsers);

        foreach($users as $user) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $user->getPassword())
            );

            $user->setIsProvisioned(true);
            $this->userRepository->persist($user);

            $io->text(sprintf('User %s provisioned.', $user->getUsername()));
        }

        $io->success(sprintf('Successfully provisioned %d user(s).', count($users)));

        return 0;
    }
}