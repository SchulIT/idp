<?php

namespace App\Oidc\EventSubscriber;

use App\Entity\OAuthClient;
use App\Entity\ServiceProvider;
use App\Repository\ServiceProviderRepositoryInterface;
use App\Security\Voter\ServiceProviderVoter;
use Exception;
use League\Bundle\OAuth2ServerBundle\Event\AuthorizationRequestResolveEvent;
use League\Bundle\OAuth2ServerBundle\OAuth2Events;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Twig\Environment;

readonly class ApproveAuthorizationRequestEventSubscriber implements EventSubscriberInterface {

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
        private ServiceProviderRepositoryInterface $serviceProviderRepository,
        private Environment $twig,
        private RequestStack $requestStack
    ) {

    }

    private function setResponse(AuthorizationRequestResolveEvent $event, ServiceProvider $serviceProvider): AuthorizationRequestResolveEvent {
        if(!in_array('text/html', $this->requestStack->getMainRequest()->getAcceptableContentTypes())) {
            return $event;
        }

        $event->setResponse(new Response(
            $this->twig->render(
                'sso/denied.html.twig', [
                    'service' => $serviceProvider,
                ]
            )
        ));

        return $event;
    }

    public function onAuthorizationRequestResolve(AuthorizationRequestResolveEvent $event): void {
        // Check if app is enabled (should always be the case as disabling is only available through CLI)
        $serviceProvider = $this->serviceProviderRepository->findOneOidcByClientIdentifier($event->getClient()->getIdentifier());

        if($serviceProvider === null) {
            throw new Exception('Service provider not found.');
        }

        if($serviceProvider->getClient() === null) {
            throw new Exception('Service provider not associated with an OIDC client.');
        }

        if(!$serviceProvider->getClient()->isActive()) {
            $this->setResponse($event, $serviceProvider);
            return;
        }

        // Check if user is granted
        $fakeToken = new UsernamePasswordToken($event->getUser(), 'fake', $event->getUser()->getRoles());
        if(!$this->accessDecisionManager->decide($fakeToken, [ ServiceProviderVoter::ENABLED ], $serviceProvider)) {
            $this->setResponse($event, $serviceProvider);
            return;
        }

        $event->resolveAuthorization(true);
    }

    #[Override]
    public static function getSubscribedEvents(): array {
        return [
            OAuth2Events::AUTHORIZATION_REQUEST_RESOLVE => 'onAuthorizationRequestResolve',
        ];
    }
}