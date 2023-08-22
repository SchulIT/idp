<?php

namespace App\Security\EmailConfirmation;

use App\Converter\UserStringConverter;
use App\Entity\EmailConfirmation;
use App\Entity\User;
use App\Repository\EmailConfirmationRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Utils\SecurityUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

readonly class ConfirmationManager {

    public function __construct(private EmailConfirmationRepositoryInterface $repository,
                                private UserRepositoryInterface $userRepository, private TranslatorInterface $translator,
                                private MailerInterface $mailer, private Environment $twig, private UserStringConverter $userConverter,
                                private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function hasConfirmation(User $user): bool {
        return $this->repository->findOneByUser($user) !== null;
    }

    public function newConfirmation(User $user, string $email): void {
        $confirmation = $this->repository->findOneByUser($user);

        if($confirmation === null) {
            $confirmation = (new EmailConfirmation())
                ->setUser($user);
        }

        $confirmation->setEmailAddress($email);

        // For security reasons: (re)generate token
        do {
            $confirmation->setToken(SecurityUtils::getRandomHexString(128));
        } while ($this->repository->findOneByToken($confirmation->getToken()) !== null);

        $this->repository->persist($confirmation);
        $context = [
            'username' => $user->getUsername(),
            'token' => $confirmation->getToken(),
            'link' => $this->urlGenerator->generate('confirm_email', [
                'token' => $confirmation->getToken()
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        ];

        $email = (new Email())
            ->subject($this->translator->trans('registration.title', [], 'mail'))
            ->to(new Address($confirmation->getEmailAddress(), $this->userConverter->convert($user)))
            ->text(
                $this->twig->render('mail/email_confirmation.txt.twig', $context)
            )
            ->html(
                $this->twig->render('mail/email_confirmation.html.twig', $context)
            );

        $this->mailer->send($email);
    }

    /**
     * @throws TokenNotFoundException
     */
    public function getConfirmation(string $token): EmailConfirmation {
        $confirmation = $this->repository->findOneByToken($token);

        if($confirmation === null) {
            throw new TokenNotFoundException($token);
        }

        return $confirmation;
    }

    /**
     * @throws EmailAddressAlreadyInUseException
     */
    public function confirm(EmailConfirmation $confirmation): void {
        $user = $confirmation->getUser();

        if($user === null) {
            return;
        }

        if($this->userRepository->findOneByEmail($confirmation->getEmailAddress()) !== null) {
            $user->setEmail(null);
            $this->userRepository->persist($user);
            $this->repository->remove($confirmation);

            throw new EmailAddressAlreadyInUseException($confirmation->getEmailAddress());
        }

        $user->setEmail($confirmation->getEmailAddress());
        $this->userRepository->persist($user);

        $this->repository->remove($confirmation);
    }
}