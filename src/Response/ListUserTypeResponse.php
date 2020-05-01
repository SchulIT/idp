<?php

namespace App\Response;

use App\Entity\UserType;
use JMS\Serializer\Annotation as Serializer;

class ListUserTypeResponse {

    /**
     * @Serializer\SerializedName("types")
     * @Serializer\Type("array<App\Entity\UserType>")
     *
     * @var UserType[]
     */
    private $types;

    /**
     * @param UserType[] $types
     */
    public function __construct(array $types) {
        $this->types = $types;
    }
}