<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;

trait UuidTrait {

    /**
     * @var UuidInterface
     */
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Serializer\ReadOnlyProperty]
    #[Serializer\Accessor(getter: 'getUuidString')]
    #[Serializer\SerializedName('uuid')]
    #[Serializer\Type('string')]
    private UuidInterface $uuid;

    public function getUuid(): UuidInterface {
        return $this->uuid;
    }

    public function getUuidString(): string {
        return (string)$this->uuid;
    }
}