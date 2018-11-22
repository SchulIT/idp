<?php

namespace App\Listener;

use App\Entity\U2fKey;
use App\Entity\User;
use App\Repository\U2fKeyRepositoryInterface;
use R\U2FTwoFactorBundle\Event\RegisterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class U2FRegistrationListener implements EventSubscriberInterface {

    private $repository;
    private $urlGenerator;

    public function __construct(U2fKeyRepositoryInterface $repository, UrlGeneratorInterface $urlGenerator) {
        $this->repository = $repository;
        $this->urlGenerator = $urlGenerator;
    }

    public function onRegister(RegisterEvent $event) {
        /** @var User $user */
        $user = $event->getUser();
        $data = $event->getRegistration();

        $key = (new U2fKey())
            ->setName($event->getKeyName())
            ->setPublicKey($data->publicKey)
            ->setKeyHandle($data->keyHandle)
            ->setCertificate($data->certificate)
            ->setCounter($data->counter)
            ->setUser($user);

        $user->addU2FKey($key);

        $this->repository->persist($key);

        $response = new RedirectResponse($this->urlGenerator->generate('two_factor'));
        $event->setResponse($response);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            'r_u2f_two_factor.register' => [
                [ 'onRegister', 0]
            ]
        ];
    }
}