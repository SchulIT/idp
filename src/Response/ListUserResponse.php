<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ListUserResponse {
    /**
     * List of objectGuids of all Active Directory users
     * @Serializer\SerializedName("users")
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $users = [ ];

    /**
     * @param string[] $users
     */
    public function __construct(array $users) {
        $this->users = $users;
    }
}