<?php

namespace App\Response;

use App\Entity\UserType;
use JMS\Serializer\Annotation as Serializer;

class ListUserTypeResponse {

    /**
     * @param UserType[] $types
     */
    public function __construct(private readonly array $types)
    {
    }

    /**
     * @return UserType[]
     */
    public function getTypes(): array {
        return $this->types;
    }
}