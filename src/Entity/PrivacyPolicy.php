<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class PrivacyPolicy {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var DateTime
     */
    private $changedAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @param DateTime $dateTime
     * @return $this
     */
    public function setChangedAt(DateTime $dateTime): PrivacyPolicy {
        $this->changedAt = $dateTime;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getChangedAt(): DateTime {
        return $this->changedAt;
    }
    /**
     * @return string|null
     */
    public function getContent(): ?string {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return PrivacyPolicy
     */
    public function setContent(?string $content): PrivacyPolicy {
        $this->content = $content;
        return $this;
    }
}