<?php

namespace App\Security\ForgotPassword;

use App\Entity\ActiveDirectoryUser;
use App\Entity\PasswordResetToken;
use App\Entity\User;
use Psr\Log\NullLogger;
use Psr\Log\Test\LoggerInterfaceTest;
use Symfony\Component\Translation\TranslatorInterface;

class ForgotPasswordManager {
    private $from;
    private $passwordManager;
    private $mailer;
    private $twig;
    private $translator;
    private $logger;

    public function __construct(string $from, PasswordManager $passwordManager, \Swift_Mailer $mailer, \Twig_Environment $twig, TranslatorInterface $translator, LoggerInterfaceTest $logger = null) {
        $this->from = $from;
        $this->passwordManager = $passwordManager;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canResetPassword(User $user) {
        return $user instanceof User && !$user instanceof ActiveDirectoryUser && $user->getEmail() !== null;
    }

    public function resetPassword(User $user) {
        if($this->canResetPassword($user) !== true) {
            return;
        }

        $token = $this->passwordManager->createPasswordToken($user);

        $content = $this->twig
            ->render('mail/reset_password.twig', [
                'token' => $token,
                'user' => $user
            ]);

        $message = (new \Swift_Message())
            ->setSubject($this->translator->trans('reset_password.title', [], 'mail'))
            ->setTo($user->getEmail())
            ->setFrom($this->from)
            ->setBody($content);

        $this->mailer->send($message);
    }

    public function updatePassword(PasswordResetToken $token, string $password) {
        $this->passwordManager->setPassword($token, $password);

        $this->logger
            ->info(sprintf('User "%s" successfully updated his/her password.', $token->getUser()->getUsername()));
    }
}