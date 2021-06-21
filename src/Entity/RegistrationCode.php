<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"code"})
 */
class RegistrationCode {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true, length=32)
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @var string|null
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var User|null
     */
    private $student;

    /**
     * The user which was created from this code.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Serializer\Exclude()
     * @var User|null
     */
    private $redeemingUser = null;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @var DateTime|null
     */
    private $validFrom;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return RegistrationCode
     */
    public function setCode(?string $code): RegistrationCode {
        $this->code = $code;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getStudent(): ?User {
        return $this->student;
    }

    /**
     * @param User|null $student
     * @return RegistrationCode
     */
    public function setStudent(?User $student): RegistrationCode {
        $this->student = $student;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getRedeemingUser(): ?User {
        return $this->redeemingUser;
    }

    /**
     * @param User|null $redeemingUser
     * @return RegistrationCode
     */
    public function setRedeemingUser(?User $redeemingUser): RegistrationCode {
        $this->redeemingUser = $redeemingUser;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getValidFrom(): ?DateTime {
        return $this->validFrom;
    }

    /**
     * @param DateTime|null $validFrom
     * @return RegistrationCode
     */
    public function setValidFrom(?DateTime $validFrom): RegistrationCode {
        $this->validFrom = $validFrom;
        return $this;
    }
}