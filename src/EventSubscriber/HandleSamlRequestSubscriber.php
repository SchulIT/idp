<?php

namespace App\EventSubscriber;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorToken;
use SchulIT\LightSamlIdpBundle\RequestStorage\RequestStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Listener which checks whether there is a SAMLRequest pending from before login. If so, the listener redirects to the
 * SSO controller in order to send the SAMLResponse (and redirect the user to the requested service).
 */
class HandleSamlRequestSubscriber implements EventSubscriberInterface {

    private UrlGeneratorInterface $urlGenerator;
    private TokenStorageInterface $tokenStorage;
    private RequestStorageInterface $samlRequestStorage;

    public function __construct(TokenStorageInterface $tokenStorage, RequestStorageInterface $requestStorage, UrlGeneratorInterface $urlGenerator) {
        $this->tokenStorage = $tokenStorage;
        $this->samlRequestStorage = $requestStorage;
        $this->urlGenerator = $urlGenerator;
    }

    public function onRequest(RequestEvent $event) {
        $request = $event->getRequest();
        $route = $request->get('_route');

        if(!$event->isMainRequest()) {
            // prevent loops
            return;
        }

        /** @var TokenInterface|null $token */
        $token = $this->tokenStorage->getToken();

        if($route === 'idp_saml') {
            $event->stopPropagation(); // stop other events from possibly redirecting
        }

        if($token === null || !$token->isAuthenticated() || $token instanceof AnonymousToken || $token instanceof TwoFactorToken || $route === 'idp_saml') {
            // prevent loops
            return;
        }

        if($this->samlRequestStorage->has() && $event->hasResponse() === false) {
            $response = new RedirectResponse($this->urlGenerator->generate('idp_saml'));
            $event->setResponse($response);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array {
        return [
            RequestEvent::class => ['onRequest', -5]
        ];
    }
}