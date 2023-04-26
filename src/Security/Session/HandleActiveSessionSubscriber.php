<?php

namespace App\Security\Session;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class HandleActiveSessionSubscriber implements EventSubscriberInterface {

    private ?ActiveSession $sessionToSave = null;

    public function __construct(private readonly Connection $connection) { }

    public function onKernelTerminate(FinishRequestEvent $event): void {
        if($this->sessionToSave === null) {
            return;
        }

        $this->connection->insert('session_user', [
            'user_id' => $this->sessionToSave->userId,
            'session_id' => $event->getRequest()->getSession()->getId(), // use current session id as it seems to be changed during the request...
            'user_agent' => mb_convert_encoding($this->sessionToSave->userAgent, 'UTF-8', 'UTF-8'), // somehow: if the user agent contains non unicode characters, it breaks :-/
            'started_at' => $this->sessionToSave->startedAt,
            'ip_address' => $this->sessionToSave->ipAddress
        ], [
            Types::INTEGER,
            Types::STRING,
            Types::TEXT,
            Types::DATETIME_IMMUTABLE,
            Types::STRING
        ]);
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void {
        $user = $event->getAuthenticationToken()->getUser();

        if(!$user instanceof User) {
            return;
        }

        /*
         * We cannot insert the data here because:
         *
         * (a) there is a lock on the sessions table which seem to prevent foreign key checks
         * (b) the session id changes during the request (which also would fail the foreign key check)
         */

        $this->sessionToSave = new ActiveSession(
            $user->getId(),
            $event->getRequest()->getSession()->getId(),
            $event->getRequest()->headers->get('User-Agent'),
            new DateTimeImmutable(),
            $event->getRequest()->getClientIp(),
            true
        );
    }

    public static function getSubscribedEvents(): array {
        return [
            InteractiveLoginEvent::class => 'onInteractiveLogin',
            FinishRequestEvent::class => 'onKernelTerminate'
        ];
    }
}