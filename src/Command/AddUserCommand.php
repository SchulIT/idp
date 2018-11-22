<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class AddUserCommand extends ContainerAwareCommand {

    private $userTypeRepository;
    private $userRepository;

    public function __construct(UserTypeRepositoryInterface $userTypeRepository, UserRepositoryInterface $userRepository, $name = null)
    {
        parent::__construct($name);
        $this->userTypeRepository = $userTypeRepository;
        $this->userRepository = $userRepository;
    }

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

        $question = new ConfirmationQuestion('Is this an administrator?');
        $isAdmin = $io->askQuestion($question);

        $app = $this->getContainer();

        /** @var EntityManager $em */
        $em = $app->get('doctrine')->getManager();

        $userTypes = $this->userTypeRepository->findAll();
        $choices = [ ];
        foreach($userTypes as $userType) {
            $choices[$userType->getId()] = $userType->getAlias();
        }

        $question = new ChoiceQuestion('Select user type', $choices);
        $selectedUserType = $io->askQuestion($question);

        $userType = $userTypes[array_search($selectedUserType, $choices)];

        $user = (new User())
            ->setUsername($username)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setType($userType);

        if($isAdmin === true) {
            $user->setRoles(['ROLE_SUPER_ADMIN']);
        }

        /** @var PasswordEncoderInterface $encoder */
        $encoder = $app->get('security.password_encoder');
        $encodedPassword = $encoder->encodePassword($user, $password);

        $user->setPassword($encodedPassword);

        $this->userRepository->persist($user);

        $io->success('User successfully added');
    }
}