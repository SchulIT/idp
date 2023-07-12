<?php

namespace App\Security\EmailConfirmation;

use App\Converter\UserStringConverter;
use App\Entity\EmailConfirmation;
use App\Entity\User;
use App\Repository\EmailConfirmationRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfirmationManager {

    private const Lifetime = '+2 hours';

    public function __construct(private DateHelper $dateHelper, private EmailConfirmationRepositoryInterface $repository, private UserRepositoryInterface $userRepository, private TranslatorInterface $translator, private MailerInterface $mailer, private UserStringConverter $userConverter, private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function hasConfirmation(User $user): bool {
        return $this->repository->findOneByUser($user) !== null;
    }

    public function newConfirmation(User $user, string $email): void {
        $confirmation = $this->repository->findOneByUser($user);

        if($confirmation !== null && $confirmation->getValidUntil() <= $this->dateHelper->getNow()) {
            return;
        }

        if($confirmation !== null) {
            $this->repository->remove($confirmation);
        }

        $confirmation = (new EmailConfirmation())
            ->setUser($user)
            ->setEmailAddress($email)
            ->setValidUntil($this->dateHelper->getNow()->modify(self::Lifetime));

        do {
            $confirmation->setToken(bin2hex(openssl_random_pseudo_bytes(64)));
        } while($this->repository->findOneByToken($confirmation->getToken()) !== null);

        $this->repository->persist($confirmation);
        $email = (new TemplatedEmail())
            ->subject($this->translator->trans('registration.title', [], 'mail'))
            ->to(new Address($confirmation->getEmailAddress(), $this->userConverter->convert($user)))
            ->textTemplate('mail/email_confirmation.txt.twig')
            ->htmlTemplate('mail/email_confirmation.html.twig')
            ->context([
                'username' => $user->getUsername(),
                'token' => $confirmation->getToken(),
                'link' => $this->urlGenerator->generate('confirm_email', [
                    'token' => $confirmation->getToken()
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'expiry_date' => $confirmation->getValidUntil()
            ]);

        $this->mailer->send($email);
    }

    /**
     * @throws TokenNotFoundException|EmailAddressAlreadyInUseException
     */
    public function confirm(string $token): void {
        $confirmation = $this->repository->findOneByToken($token);

        if($confirmation === null) {
            throw new TokenNotFoundException($token);
        }

        $user = $confirmation->getUser();
        $user->setEmail($confirmation->getEmailAddress());
        $user->setIsEmailConfirmationPending(false);

        if($this->userRepository->findOneByEmail($confirmation->getEmailAddress()) !== null) {
            $user->setEmail(null);
            $this->userRepository->persist($user);
            $this->repository->remove($confirmation);

            throw new EmailAddressAlreadyInUseException($confirmation->getEmailAddress());
        }

        $this->userRepository->persist($user);
        $this->repository->remove($confirmation);
    }

    public function removeOldConfirmations(): int {
        return $this->repository->removeExpired($this->dateHelper->getNow());
    }
}