<?php

namespace App\Security\EmailConfirmation;

use App\Entity\EmailConfirmation;
use App\Entity\User;
use App\Repository\EmailConfirmationRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Swift_Mailer;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ConfirmationManager {

    private const Lifetime = '+2 hours';

    private $from;
    private $dateHelper;
    private $repository;
    private $userRepository;
    private $translator;
    private $mailer;
    private $twig;

    public function __construct(string $from, DateHelper $dateHelper, EmailConfirmationRepositoryInterface $repository, UserRepositoryInterface $userRepository,
                                TranslatorInterface $translator, Swift_Mailer $mailer, Environment $twig) {
        $this->from = $from;
        $this->dateHelper = $dateHelper;
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->twig = $twig;
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

        $content = $this->twig
            ->render('mail/email_confirmation.html.twig', [
                'token' => $confirmation->getToken(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'expiry_date' => $confirmation->getValidUntil()
            ]);

        $message = (new \Swift_Message())
            ->setSubject($this->translator->trans('registration.title', [], 'mail'))
            ->setTo($user->getEmail())
            ->setFrom($this->from)
            ->setBody($content);

        $this->mailer->send($message);
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