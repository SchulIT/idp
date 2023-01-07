<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class PrivacyPolicy {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private ?DateTime $changedAt = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function setChangedAt(DateTime $dateTime): PrivacyPolicy {
        $this->changedAt = $dateTime;
        return $this;
    }

    public function getChangedAt(): DateTime {
        return $this->changedAt;
    }
    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $content): PrivacyPolicy {
        $this->content = $content;
        return $this;
    }
}