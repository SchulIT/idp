<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

readonly class ListUserTypeResponse {

    /**
     * @param UserType[] $types
     */
    public function __construct(#[Serializer\Type("array<App\Response\UserType>")]
    public array $types)
    {
    }

}