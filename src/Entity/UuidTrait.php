<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;

trait UuidTrait {

    /**
     * @Serializer\ReadOnlyProperty()
     * @Serializer\Accessor(getter="getUuidString")
     * @Serializer\SerializedName("uuid")
     * @Serializer\Type("string")
     * @var UuidInterface
     */
    #[ORM\Column(type: 'uuid', unique: true)]
    private $uuid;

    public function getUuid(): UuidInterface {
        return $this->uuid;
    }

    public function getUuidString(): string {
        return (string)$this->uuid;
    }
}