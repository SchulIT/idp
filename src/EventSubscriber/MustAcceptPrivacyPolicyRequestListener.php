<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\PrivacyPolicyRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use DateTime;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorToken;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MustAcceptPrivacyPolicyRequestListener implements EventSubscriberInterface {

    public const RedirectFallbackRoute = 'dashboard';
    public const LogoutRoute = 'logout';
    public const AcceptPrivacyRoute = 'show_privacy_policy';

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator, private readonly TokenStorageInterface $tokenStorage, private readonly RequestStack $requestStack, private readonly PrivacyPolicyRepositoryInterface $privacyPolicyRepository, private readonly CsrfTokenManagerInterface $csrfTokenManager, private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function onRequest(RequestEvent $event): void {
        if($event->isMainRequest() === false) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if($token === null || $token instanceof TwoFactorToken || !$token->getUser() instanceof User) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');

        /** @var User $user */
        $user = $token->getUser();

        if($currentRoute === static::LogoutRoute) {
            return;
        }

        $session = $this->requestStack->getSession();

        if($currentRoute === static::AcceptPrivacyRoute) {
            if ($request->getMethod() === 'POST') {
                $token = $request->request->get('_csrf_token');
                if ($this->csrfTokenManager->isTokenValid(new CsrfToken('privacy_policy', $token))) {
                    $user->setPrivacyPolicyConfirmedAt(new DateTime());
                    $this->userRepository->persist($user);

                    $uri = $session->has('privacy.referrer') ? $session->get('privacy.referrer') : $this->urlGenerator->generate(static::RedirectFallbackRoute);

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
            }
            return;
        }

        $policy = $this->privacyPolicyRepository->findOne();

        if($policy === null) {
            return;
        }

        if($policy->getChangedAt() < $user->getPrivacyPolicyConfirmedAt()) {
            return;
        }

        $session->set('privacy.referrer', $request->getPathInfo());

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
    public static function getSubscribedEvents(): array {
        return [
            RequestEvent::class => ['onRequest', -2]
        ];
    }
}