<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Entity\UserType;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use RuntimeException;
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

#[AsCommand(name: 'app:add-user', description: 'Erstellt einen neuen Benutzer')]
class AddUserCommand extends Command {

    public function __construct(private readonly UserTypeRepositoryInterface $userTypeRepository, private readonly UserRepositoryInterface $userRepository,
                                private readonly UserPasswordHasherInterface $passwordHasher, private readonly ValidatorInterface $validator, ?string $name = null)
    {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $username = $io->ask('Username', null, function($username) {
            if(empty($username)) {
                throw new RuntimeException('Benutzername darf nicht leer sein.');
            }

            if($this->validator->validate($username, new Email())->count() > 0) {
                throw new RuntimeException('Benutzername muss eine E-Mail-Adresse sein.');
            }

            return $username;
        });

        $firstname = $io->ask('Vorname', null, function($firstname) {
            if(empty($firstname)) {
                throw new RuntimeException('Vorname darf nicht leer sein.');
            }

            return $firstname;
        });

        $lastname = $io->ask('Nachname', null, function($lastname) {
            if(empty($lastname)) {
                throw new RuntimeException('Nachname darf nicht leer sein.');
            }

            return $lastname;
        });

        $email = $io->ask('E-Mail', null, function($email) {
            if(empty($email)) {
                throw new RuntimeException('E-Mail darf nicht leer sein.');
            }

            return $email;
        });

        $password = $io->askHidden('Passwort', function($password) {
            if(empty($password)) {
                throw new RuntimeException('Passwort darf nicht leer sein.');
            }

            return $password;
        });

        $io->askHidden('Repeat Password', function($repeatPassword) use ($password) {
            if($repeatPassword !== $password) {
                throw new RuntimeException('Passwörter müssen übereinstimmen.');
            }

            return $repeatPassword;
        });

        $question = new ConfirmationQuestion('Ist der Benutzer ein Administrator?');
        $isAdmin = $io->askQuestion($question);

        $userTypes = $this->userTypeRepository->findAll();
        $choices = array_map(fn(UserType $type): string => $type->getAlias(), $userTypes);

        $question = new ChoiceQuestion('Benutzertyp wählen', $choices, 0);
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

        $io->success('Benutzer erfolgreich erstellt');

        return Command::SUCCESS;
    }
}
