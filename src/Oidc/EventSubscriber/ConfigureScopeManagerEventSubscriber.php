<?php

namespace App\Oidc\EventSubscriber;

use App\Oidc\ScopeResolver;
use League\Bundle\OAuth2ServerBundle\Manager\ScopeManagerInterface;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

readonly class ConfigureScopeManagerEventSubscriber implements EventSubscriberInterface {

    public function __construct(
        private ScopeResolver $scopeResolver,
        private ScopeManagerInterface $scopeManager
    ) {

    }

    public function onRequest(RequestEvent $event): void {
        foreach($this->scopeResolver->getScopes() as $scope) {
            $this->scopeManager->save($scope);
        }
    }

    #[Override]
    public static function getSubscribedEvents(): array {
        return [
            RequestEvent::class => 'onRequest',
        ];
    }
}