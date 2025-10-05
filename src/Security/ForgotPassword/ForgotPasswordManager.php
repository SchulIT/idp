<?php

declare(strict_types=1);

namespace App\Security\ForgotPassword;

use App\Entity\ActiveDirectoryUser;
use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Repository\PasswordResetTokenRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Utils\SecurityUtils;
use SchulIT\CommonBundle\Helper\DateHelper;
use SensitiveParameter;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ForgotPasswordManager {

    /**
     * Lifetime of the password request token.
     */
    public const LifeTimeInMinutes = 120;

    public function __construct(private readonly PasswordResetTokenRepositoryInterface $passwordResetTokenRepository, private readonly Environment $twig, private readonly MailerInterface $mailer,
                                private readonly UrlGeneratorInterface $urlGenerator, private readonly TranslatorInterface $translator, private readonly DateHelper $dateHelper,
                                private readonly UserPasswordHasherInterface $passwordHasher, private readonly UserRepositoryInterface $userRepository) {

    }

    /**
     * @throws UserCannotResetPasswordException Is thrown in case the user is either an Active Directory user or the user has not provided any email address.
     * @throws TooManyRequestsException In case there is a non-expired password reset token for the given user
     */
    public function createPasswordResetRequest(User $user, ?string $emailAddress): PasswordResetToken {
        if($user instanceof ActiveDirectoryUser) {
            throw new UserCannotResetPasswordException(Reason::ActiveDirectoryUser);
        }

        if($emailAddress === null || $emailAddress === '' || $emailAddress === '0') {
            throw new UserCannotResetPasswordException(Reason::NoEmailAddress);
        }

        $existingToken = $this->passwordResetTokenRepository->findMostRecentNonExpired($user);

        if($existingToken instanceof PasswordResetToken) {
            throw new TooManyRequestsException();
        }

        $token = $this->createToken($user);
        $this->passwordResetTokenRepository->persist($token);

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
            ->to($emailAddress)
            ->subject($this->translator->trans('reset_password.title', [], 'mail'))
            ->text(
                $this->twig->render('mail/reset_password.txt.twig', $context)
            )
            ->html(
                $this->twig->render('mail/reset_password.html.twig', $context)
            );

        $this->mailer->send($email);

        return $token;
    }

    protected function createToken(User $user): PasswordResetToken {
        return (new PasswordResetToken())
            ->setUser($user)
            ->setExpiresAt(
                $this->dateHelper->getNow()->modify(sprintf('+%d minutes', self::LifeTimeInMinutes))
            )
            ->setToken(SecurityUtils::getRandomHexString(64));
    }

    /**
     * @throws TokenExpiredException
     */
    public function updatePassword(PasswordResetToken $token, #[SensitiveParameter] string $newPassword): void {
        if($token < $this->dateHelper->getNow()) {
            throw new TokenExpiredException();
        }

        $user = $token->getUser();
        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $this->userRepository->persist($user);

        $this->passwordResetTokenRepository->remove($token);
    }

    public function garbageCollect(): int {
        return $this->passwordResetTokenRepository->removeExpired();
    }

    public function getToken(string $token): ?PasswordResetToken {
        return $this->passwordResetTokenRepository->findOneByToken($token);
    }
}
