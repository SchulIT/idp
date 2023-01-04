<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ListActiveDirectoryUserResponse {

    /**
     * @var ActiveDirectoryUser[]
     */
    #[Serializer\Type("array<App\Response\ActiveDirectoryUser>")]
    public readonly array $users;

    /**
     * @param ActiveDirectoryUser[] $users
     */
    public function __construct(array $users) {
        $this->users = $users;
    }
}