<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MustChangePasswordRedirectEventSubscriber implements EventSubscriberInterface {

    private const RedirectRoute = 'profile_password';

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator, private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function onRequest(RequestEvent $event): void {
        if($event->isMainRequest() !== true) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if($token === null) {
            return;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return;
        }

        if($user->isMustChangePassword() === false) {
            return;
        }

        $currentRoute = $event->getRequest()->attributes->get('_route');

        if($currentRoute === self::RedirectRoute) {
            // Do not create redirect loops
            $event->stopPropagation(); // prevent other events of redirecting
            return;
        }

        $response = new RedirectResponse(
            $this->urlGenerator->generate(self::RedirectRoute),
            Response::HTTP_FOUND
        );

        $event->setResponse($response);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            RequestEvent::class => ['onRequest', -1]
        ];
    }
}