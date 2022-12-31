<?php

namespace App\Security\Session;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

/**
 * Extends the database schema to allow storing user
 */
class ActiveSessionsDoctrineSchemaSubscriber implements EventSubscriber {

    public function __construct(private readonly ActiveSessionsResolver $sessionsResolver) {

    }

    public function postGenerateSchema(GenerateSchemaEventArgs $event): void {
        $this->sessionsResolver->configureSchema($event->getSchema(), $event->getEntityManager()->getConnection());
    }

    public function getSubscribedEvents(): array {
        return [
            ToolEvents::postGenerateSchema
        ];
    }
}