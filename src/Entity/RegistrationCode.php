<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[UniqueEntity(fields: ['code'])]
#[SoftDeleteable(fieldName: 'deletedAt', hardDelete: false)]
class RegistrationCode {

    use IdTrait;
    use UuidTrait;
    use SoftDeleteableEntity;

    #[ORM\Column(type: 'string', length: 32, unique: true)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $code = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?User $student = null;

    /**
     * The user which was created from this code.
     */
    #[Serializer\Exclude]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $redeemingUser = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $validFrom = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getCode(): ?string {
        return $this->code;
    }

    public function setCode(?string $code): RegistrationCode {
        $this->code = $code;
        return $this;
    }

    public function getStudent(): ?User {
        return $this->student;
    }

    public function setStudent(?User $student): RegistrationCode {
        $this->student = $student;
        return $this;
    }

    public function getRedeemingUser(): ?User {
        return $this->redeemingUser;
    }

    public function setRedeemingUser(?User $redeemingUser): RegistrationCode {
        $this->redeemingUser = $redeemingUser;
        return $this;
    }

    public function getValidFrom(): ?DateTime {
        return $this->validFrom;
    }

    public function setValidFrom(?DateTime $validFrom): RegistrationCode {
        $this->validFrom = $validFrom;
        return $this;
    }
}