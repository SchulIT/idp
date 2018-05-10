<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\UserType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class AddUserCommand extends ContainerAwareCommand {
    public function configure() {
        $this
            ->setName('app:add-user')
            ->setDescription('Adds a new user');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        $username = $io->ask('Username', null, function($username) {
            if(empty($username)) {
                throw new \RuntimeException('Username must not be empty');
            }

            return $username;
        });

        $firstname = $io->ask('Firstname', null, function($firstname) {
            if(empty($firstname)) {
                throw new \RuntimeException('Firstname must not be empty');
            }

            return $firstname;
        });

        $lastname = $io->ask('Lastname', null, function($lastname) {
            if(empty($lastname)) {
                throw new \RuntimeException('Lastname must not be empty');
            }

            return $lastname;
        });

        $email = $io->ask('E-Mail', null, function($email) {
            if(empty($email)) {
                throw new \RuntimeException('E-Mail must not be empty');
            }

            return $email;
        });

        $password = $io->askHidden('Password', function($password) {
            if(empty($password)) {
                throw new \RuntimeException('Password must not be empty');
            }

            return $password;
        });

        $io->askHidden('Repeat Password', function($repeatPassword) use ($password) {
            if($repeatPassword !== $password) {
                throw new \RuntimeException('Passwords must match');
            }

            return $repeatPassword;
        });

        $app = $this->getContainer();

        /** @var EntityManager $em */
        $em = $app->get('doctrine')->getManager();

        $userType = $em->getRepository(UserType::class)
            ->findOneById(1);

        $user = (new User())
            ->setUsername($username)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setType($userType);

        /** @var PasswordEncoderInterface $encoder */
        $encoder = $app->get('security.password_encoder');
        $encodedPassword = $encoder->encodePassword($user, $password);

        $user->setPassword($encodedPassword);

        $em->persist($user);
        $em->flush();

        $io->success('User successfully added');
    }
}