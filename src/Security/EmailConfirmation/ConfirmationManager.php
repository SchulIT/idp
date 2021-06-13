<?php

namespace App\Security\EmailConfirmation;

use App\Converter\UserStringConverter;
use App\Entity\EmailConfirmation;
use App\Entity\User;
use App\Repository\EmailConfirmationRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfirmationManager {

    private const Lifetime = '+2 hours';

    private $dateHelper;
    private $repository;
    private $userRepository;
    private $translator;
    private $mailer;
    private $userConverter;

    public function __construct(DateHelper $dateHelper, EmailConfirmationRepositoryInterface $repository, UserRepositoryInterface $userRepository,
                                TranslatorInterface $translator, MailerInterface $mailer, UserStringConverter $userConverter) {
        $this->dateHelper = $dateHelper;
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->userConverter = $userConverter;
    }

    public function hasConfirmation(User $user): bool {
        return $this->repository->findOneByUser($user) !== null;
    }

    public function newConfirmation(User $user, string $email): void {
        $confirmation = $this->repository->findOneByUser($user);

        if($confirmation !== null && $confirmation->getValidUntil() <= $this->dateHelper->getNow()) {
            $this->repository->remove($confirmation);
        } else {
            $confirmation = (new EmailConfirmation())
                ->setUser($user)
                ->setEmailAddress($email)
                ->setValidUntil($this->dateHelper->getNow()->modify(static::Lifetime));

            do {
                $confirmation->setToken(bin2hex(openssl_random_pseudo_bytes(64)));
            } while($this->repository->findOneByToken($confirmation->getToken()) !== null);
        }

        $this->repository->persist($confirmation);
        $email = (new TemplatedEmail())
            ->subject($this->translator->trans('registration.title', [], 'mail'))
            ->to(new Address($user->getEmail(), $this->userConverter->convert($user)))
            ->textTemplate('mail/email_confirmation.txt.twig')
            ->htmlTemplate('mail/email_confirmation.html.twig')
            ->context([
                'username' => $user->getUsername(),
                'token' => $confirmation->getToken(),
                'expiry_date' => $confirmation->getValidUntil()
            ]);

        $this->mailer->send($email);
    }

    /**
     * @param string $token
     * @throws TokenNotFoundException
     */
    public function confirm(string $token): void {
        $confirmation = $this->repository->findOneByToken($token);

        if($confirmation === null) {
            throw new TokenNotFoundException($token);
        }

        $user = $confirmation->getUser();
        $user->setEmail($confirmation->getEmailAddress());

        $this->userRepository->persist($user);
        $this->repository->remove($confirmation);
    }

    public function removeOldConfirmations(): int {
        return $this->repository->removeExpired($this->dateHelper->getNow());
    }
}