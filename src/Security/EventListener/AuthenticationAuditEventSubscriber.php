<?php

namespace App\Security\EventListener;

use App\Entity\AuthenticationAudit;
use App\Entity\AuthenticationAuditType;
use Chrisguitarguy\RequestId\RequestIdStorage;
use Doctrine\ORM\EntityManagerInterface;
use GeoIp2\Database\Reader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class AuthenticationAuditEventSubscriber implements EventSubscriberInterface {

    /**
     * We need to store the authentication audit due to possible database locking errors during
     * security events.
     *
     * @var AuthenticationAudit|null
     */
    private ?AuthenticationAudit $auditToStore = null;

    public function __construct(private readonly bool $isEnabled,
                                private readonly EntityManagerInterface $em,
                                private readonly TranslatorInterface $translator,
                                private readonly RequestIdStorage $requestIdStorage,
                                private readonly Reader $reader) {

    }

    private function createAudit(Request $request): AuthenticationAudit {
        return (new AuthenticationAudit())
            ->setRequestId($this->requestIdStorage->getRequestId())
            ->setIpAddress($request->getClientIp());
    }

    public function onKernelTerminate(): void {
        if($this->isEnabled !== true) {
            return; // In case auditing is not enabled, do not persist anything
        }

        if($this->auditToStore !== null) {
            try {
                $this->auditToStore->setIpCountry($this->reader->country($this->auditToStore->getIpAddress())->country->isoCode);
            } catch (Throwable) { }

            $this->em->persist($this->auditToStore);
            $this->em->flush();

            $this->auditToStore = null;
        }
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void {
        if($this->auditToStore !== null) {
            return;
        }

        $audit = $this->createAudit($event->getRequest());
        $audit->setUsername($event->getUser()->getUserIdentifier());
        $audit->setAuthenticatorFqcn(get_class($event->getAuthenticator()));
        $audit->setTokenFqcn(get_class($event->getAuthenticatedToken()));
        $audit->setFirewall($event->getFirewallName());
        $audit->setType(AuthenticationAuditType::Login);

        $this->auditToStore = $audit;
    }

    public function onLogout(LogoutEvent $event): void {
        $audit = $this->createAudit($event->getRequest());
        $audit->setType(AuthenticationAuditType::Logout);

        if($event->getToken() !== null) {
            $audit->setUsername($event->getToken()->getUserIdentifier());
        }

        $this->auditToStore = $audit;
    }

    public function onSwitchUser(SwitchUserEvent $event): void {
        $audit = $this->createAudit($event->getRequest());
        $audit->setType(AuthenticationAuditType::SwitchUser);
        $audit->setTokenFqcn(get_class($event->getToken()));

        $audit->setUsername($event->getToken()->getUserIdentifier());
        $token = $event->getToken();

        if($token instanceof SwitchUserToken) {
            // Impersonation started
            $audit->setMessage($token->getOriginalToken()->getUserIdentifier());
        } else {
            $audit->setMessage('Impersonation beendet.');
        }

        $this->auditToStore = $audit;
    }

    public function onLoginFailure(LoginFailureEvent $event): void {
        $audit = $this->createAudit($event->getRequest());
        $audit->setType(AuthenticationAuditType::Error);

        $audit->setMessage(
            $this->translator->trans($event->getException()->getMessageKey(), $event->getException()->getMessageData())
        );

        $username = $event->getRequest()->request->get('_username');
        $audit->setUsername($username);

        $this->auditToStore = $audit;
    }

    public static function getSubscribedEvents(): array {
        return [
            SwitchUserEvent::class => 'onSwitchUser',
            LoginFailureEvent::class => 'onLoginFailure',
            LoginSuccessEvent::class => 'onLoginSuccess',
            LogoutEvent::class => 'onLogout',
            TerminateEvent::class => 'onKernelTerminate'
        ];
    }
}