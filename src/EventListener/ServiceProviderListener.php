<?php

namespace App\EventListener;

use App\Entity\ServiceProvider;
use App\Service\ServiceProviderTokenGenerator;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class ServiceProviderListener implements EventSubscriber {

    private $tokenGenerator;

    public function __construct(ServiceProviderTokenGenerator $serviceProviderTokenGenerator) {
        $this->tokenGenerator = $serviceProviderTokenGenerator;
    }

    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if(!$entity instanceof ServiceProvider) {
            return;
        }

        if(empty($entity->getToken())) {
            $entity->setToken($this->tokenGenerator->generateToken());
        }
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents() {
        return [
            Events::prePersist
        ];
    }
}