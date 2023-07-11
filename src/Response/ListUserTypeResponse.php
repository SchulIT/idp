<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

readonly class ListUserTypeResponse {

    /**  @var UserType[] */
    #[Serializer\Type("array<App\Response\UserType>")]
    public array $types;

    /**
     * @param UserType[] $types
     */
    public function __construct(array $types) {
        $this->types = $types;
    }

}