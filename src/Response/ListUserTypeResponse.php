<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ListUserTypeResponse {

    /**  @var UserType[] */
    #[Serializer\Type("array<App\Response\UserType>")]
    public readonly array $types;

    /**
     * @param UserType[] $types
     */
    public function __construct(array $types) {
        $this->types = $types;
    }

}