<?php

namespace App\Listener;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorToken;
use SchoolIT\LightSamlIdpBundle\RequestStorage\RequestStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class HandleSamlRequestListener implements EventSubscriberInterface {

    private $urlGenerator;
    private $tokenStorage;
    private $samlRequestStorage;

    public function __construct(TokenStorageInterface $tokenStorage, RequestStorageInterface $requestStorage, UrlGeneratorInterface $urlGenerator) {
        $this->tokenStorage = $tokenStorage;
        $this->samlRequestStorage = $requestStorage;
        $this->urlGenerator = $urlGenerator;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
        $route = $request->get('_route');

        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        if($token === null || !$token->isAuthenticated() || $token instanceof AnonymousToken || $token instanceof TwoFactorToken || $route === 'idp_saml') {
            // prevent loops
            return;
        }

        if($this->samlRequestStorage->has()) {
            $response = new RedirectResponse($this->urlGenerator->generate('idp_saml'));
            $event->setResponse($response);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents() {
        return [
            KernelEvents::REQUEST => [
                [ 'onKernelRequest', 0]
            ]
        ];
    }
}