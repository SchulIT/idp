<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"suffix"})
 */
class ActiveDirectoryUpnSuffix {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @var string|null
     */
    private $suffix;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string|null
     */
    private $description;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string|null
     */
    public function getSuffix(): ?string {
        return $this->suffix;
    }

    /**
     * @param string|null $suffix
     * @return ActiveDirectoryUpnSuffix
     */
    public function setSuffix(?string $suffix): ActiveDirectoryUpnSuffix {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return ActiveDirectoryUpnSuffix
     */
    public function setDescription(?string $description): ActiveDirectoryUpnSuffix {
        $this->description = $description;
        return $this;
    }
}