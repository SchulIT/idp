<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

trait UuidTrait {

    /**
     * @ORM\Column(type="uuid", unique=true)
     * @var UuidInterface
     */
    private $uuid;

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface {
        return $this->uuid;
    }
}