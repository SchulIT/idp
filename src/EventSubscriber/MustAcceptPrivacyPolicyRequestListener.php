<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\PrivacyPolicyRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MustAcceptPrivacyPolicyRequestListener implements EventSubscriberInterface {

    const RedirectFallbackRoute = 'dashboard';
    const LogoutRoute = 'logout';
    const AcceptPrivacyRoute = 'show_privacy_policy';

    private $urlGenerator;
    private $tokenStorage;
    private $privacyPolicyRepository;
    private $userRepository;
    private $csrfTokenManager;
    private $session;

    public function __construct(UrlGeneratorInterface $urlGenerator, TokenStorageInterface $tokenStorage,
                                PrivacyPolicyRepositoryInterface $privacyPolicyRepository, CsrfTokenManagerInterface $csrfTokenManager, UserRepositoryInterface $userRepository,
                                SessionInterface $session) {
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
        $this->privacyPolicyRepository = $privacyPolicyRepository;
        $this->userRepository = $userRepository;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->session = $session;
    }

    public function onRequest(RequestEvent $event) {
        if($event->isMasterRequest() === false) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if($token === null || !$token->getUser() instanceof User) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');

        /** @var User $user */
        $user = $token->getUser();

        if($currentRoute === static::LogoutRoute) {
            return;
        }

        if($currentRoute === static::AcceptPrivacyRoute) {
            if ($request->getMethod() === 'POST') {
                $token = $request->request->get('_csrf_token');
                if ($this->csrfTokenManager->isTokenValid(new CsrfToken('privacy_policy', $token))) {
                    $user->setPrivacyPolicyConfirmedAt(new DateTime());
                    $this->userRepository->persist($user);

                    $uri = $this->session->has('privacy.referrer') ? $this->session->get('privacy.referrer') : $this->urlGenerator->generate(static::RedirectFallbackRoute);

                    if($uri === $this->urlGenerator->generate(static::AcceptPrivacyRoute)) {
                        $uri = $this->urlGenerator->generate(static::RedirectFallbackRoute);
                    }

                    $response = new RedirectResponse(
                        $uri,
                        Response::HTTP_TEMPORARY_REDIRECT
                    );

                    $event->setResponse($response);
                    return;
                }
            } else {
                return;
            }
        }

        if($user->getPrivacyPolicyConfirmedAt() === null) {
            return;
        }

        $policy = $this->privacyPolicyRepository->findOne();

        if($policy === null) {
            return;
        }

        if($policy->getChangedAt() < $user->getPrivacyPolicyConfirmedAt()) {
            return;
        }

        $this->session->set('privacy.referrer', $request->getPathInfo());

        $response = new RedirectResponse(
            $this->urlGenerator->generate(static::AcceptPrivacyRoute),
            Response::HTTP_TEMPORARY_REDIRECT
        );

        if(!$event->hasResponse()) {
            $event->setResponse($response);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            RequestEvent::class => ['onRequest', -2]
        ];
    }
}