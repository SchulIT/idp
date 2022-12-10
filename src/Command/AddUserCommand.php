<?php

namespace App\Command;

use RuntimeException;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'app:add-user', description: 'Adds a new user')]
class AddUserCommand extends Command {

    public function __construct(private UserTypeRepositoryInterface $userTypeRepository, private UserRepositoryInterface $userRepository,
                                private UserPasswordHasherInterface $passwordHasher, private ValidatorInterface $validator, $name = null)
    {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $username = $io->ask('Username', null, function($username) {
            if(empty($username)) {
                throw new RuntimeException('Username must not be empty');
            }

            if($this->validator->validate($username, new Email())->count() > 0) {
                throw new RuntimeException('Username must be an email address.');
            }

            return $username;
        });

        $firstname = $io->ask('Firstname', null, function($firstname) {
            if(empty($firstname)) {
                throw new RuntimeException('Firstname must not be empty');
            }

            return $firstname;
        });

        $lastname = $io->ask('Lastname', null, function($lastname) {
            if(empty($lastname)) {
                throw new RuntimeException('Lastname must not be empty');
            }

            return $lastname;
        });

        $email = $io->ask('E-Mail', null, function($email) {
            if(empty($email)) {
                throw new RuntimeException('E-Mail must not be empty');
            }

            return $email;
        });

        $password = $io->askHidden('Password', function($password) {
            if(empty($password)) {
                throw new RuntimeException('Password must not be empty');
            }

            return $password;
        });

        $io->askHidden('Repeat Password', function($repeatPassword) use ($password) {
            if($repeatPassword !== $password) {
                throw new RuntimeException('Passwords must match');
            }

            return $repeatPassword;
        });

        $question = new ConfirmationQuestion('Is this an administrator?');
        $isAdmin = $io->askQuestion($question);

        $userTypes = $this->userTypeRepository->findAll();
        $choices = array_map(fn(UserType $type) => $type->getAlias(), $userTypes);

        $question = new ChoiceQuestion('Select user type', $choices, 0);
        $selectedUserTypeAlias = $io->askQuestion($question);
        $userType = $userTypes[array_search($selectedUserTypeAlias, $choices)];

        $user = (new User())
            ->setUsername($username)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setType($userType);

        if($isAdmin === true) {
            $user->setRoles(['ROLE_SUPER_ADMIN']);
        }

        $encodedPassword = $this->passwordHasher->hashPassword($user, $password);

        $user->setPassword($encodedPassword);

        $this->userRepository->persist($user);

        $io->success('User successfully added');

        return 0;
    }
}