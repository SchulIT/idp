<?php

namespace App\Security\ForgotPassword;

use App\Converter\UserStringConverter;
use App\Entity\ActiveDirectoryUser;
use App\Entity\PasswordResetToken;
use App\Entity\User;
use Psr\Log\NullLogger;
use Psr\Log\Test\LoggerInterfaceTest;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ForgotPasswordManager {
    private $passwordManager;
    private $mailer;
    private $translator;
    private $userConverter;
    private $logger;

    public function __construct(PasswordManager $passwordManager, MailerInterface $mailer, TranslatorInterface $translator,
                                UserStringConverter $userConverter, LoggerInterfaceTest $logger = null) {
        $this->passwordManager = $passwordManager;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->userConverter = $userConverter;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canResetPassword(User $user, ?string $email) {
        return $user instanceof User && !$user instanceof ActiveDirectoryUser && $email !== null;
    }

    public function resetPassword(?User $user, ?string $email) {
        if($user === null) {
            return;
        }

        if($this->canResetPassword($user, $email) !== true) {
            return;
        }

        $token = $this->passwordManager->createPasswordToken($user);

        $email = (new TemplatedEmail())
            ->to(new Address($email, $this->userConverter->convert($user)))
            ->subject($this->translator->trans('reset_password.title', [], 'mail'))
            ->textTemplate('mail/reset_password.txt.twig')
            ->htmlTemplate('mail/reset_password.html.twig')
            ->context([
                'token' => $token,
                'username' => $user->getUsername()
            ]);

        $this->mailer->send($email);
    }

    public function updatePassword(PasswordResetToken $token, string $password) {
        $this->passwordManager->setPassword($token, $password);

        $this->logger
            ->info(sprintf('User "%s" successfully updated his/her password.', $token->getUser()->getUsername()));
    }
}