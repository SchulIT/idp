<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

readonly class ListActiveDirectoryUserResponse {

    /**
     * @var ActiveDirectoryUser[]
     */
    #[Serializer\Type("array<App\Response\ActiveDirectoryUser>")]
    public array $users;

    /**
     * @param ActiveDirectoryUser[] $users
     */
    public function __construct(array $users) {
        $this->users = $users;
    }
}