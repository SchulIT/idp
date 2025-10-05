<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
class AuthenticationAudit {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $username = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $ipCountry = null;

    #[Column(name: '`type`', type: 'string', nullable: true, enumType: AuthenticationAuditType::class)]
    private ?AuthenticationAuditType $type = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $authenticatorFqcn = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $tokenFqcn = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $firewall = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $requestId = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function setUsername(?string $username): AuthenticationAudit {
        $this->username = $username;
        return $this;
    }

    public function getIpAddress(): ?string {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): AuthenticationAudit {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getIpCountry(): ?string {
        return $this->ipCountry;
    }

    public function setIpCountry(?string $ipCountry): AuthenticationAudit {
        $this->ipCountry = $ipCountry;
        return $this;
    }

    public function getType(): ?AuthenticationAuditType {
        return $this->type;
    }

    public function setType(?AuthenticationAuditType $type): AuthenticationAudit {
        $this->type = $type;
        return $this;
    }

    public function getMessage(): ?string {
        return $this->message;
    }

    public function setMessage(?string $message): AuthenticationAudit {
        $this->message = $message;
        return $this;
    }

    public function getAuthenticatorFqcn(): ?string {
        return $this->authenticatorFqcn;
    }

    public function setAuthenticatorFqcn(?string $authenticatorFqcn): AuthenticationAudit {
        $this->authenticatorFqcn = $authenticatorFqcn;
        return $this;
    }

    public function getTokenFqcn(): ?string {
        return $this->tokenFqcn;
    }

    public function setTokenFqcn(?string $tokenFqcn): AuthenticationAudit {
        $this->tokenFqcn = $tokenFqcn;
        return $this;
    }

    public function getFirewall(): ?string {
        return $this->firewall;
    }

    public function setFirewall(?string $firewall): AuthenticationAudit {
        $this->firewall = $firewall;
        return $this;
    }

    public function getRequestId(): ?string {
        return $this->requestId;
    }

    public function setRequestId(?string $requestId): AuthenticationAudit {
        $this->requestId = $requestId;
        return $this;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }
}
