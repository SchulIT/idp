<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

readonly class ListActiveDirectoryUserResponse {

    /**
     * @param ActiveDirectoryUser[] $users
     */
    public function __construct(#[Serializer\Type("array<App\Response\ActiveDirectoryUser>")]
    public array $users)
    {
    }
}