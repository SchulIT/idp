<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ListUserResponse {

    /** UUIDs der Benutzer */
    #[Serializer\Type("array<string>")]
    public readonly array $users;

    public function __construct(array $users) {
        $this->users = $users;
    }

}