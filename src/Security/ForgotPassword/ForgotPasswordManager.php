<?php

namespace App\Security\ForgotPassword;

use App\Converter\UserStringConverter;
use App\Entity\ActiveDirectoryUser;
use App\Entity\PasswordResetToken;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ForgotPasswordManager {
    private LoggerInterface $logger;

    public function __construct(private readonly PasswordManager $passwordManager, private readonly MailerInterface $mailer, private readonly TranslatorInterface $translator,
                                private readonly UserStringConverter $userConverter, private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly Environment $twig, LoggerInterface $logger = null) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function canResetPassword(User $user, ?string $email): bool {
        return $user instanceof User && !$user instanceof ActiveDirectoryUser && $email !== null;
    }

    public function resetPassword(?User $user, ?string $email): void {
        if($user === null) {
            return;
        }

        if($this->canResetPassword($user, $email) !== true) {
            return;
        }

        $token = $this->passwordManager->createPasswordToken($user);
        $context = [
            'token' => $token->getToken(),
            'link' => $this->urlGenerator->generate(
                'change_password', [
                'token' => $token->getToken()
            ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'expiry_date' => $token->getExpiresAt(),
            'username' => $user->getUsername()
        ];

        $email = (new Email())
            ->to(new Address($email, $this->userConverter->convert($user)))
            ->subject($this->translator->trans('reset_password.title', [], 'mail'))
            ->text(
                $this->twig->render('mail/reset_password.txt.twig', $context)
            )
            ->html(
                $this->twig->render('mail/reset_password.html.twig', $context)
            );

        $this->mailer->send($email);
    }

    public function updatePassword(PasswordResetToken $token, string $password): void {
        $this->passwordManager->setPassword($token, $password);

        $this->logger
            ->info(sprintf('User "%s" successfully updated his/her password.', $token->getUser()->getUsername()));
    }
}