<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

readonly class ListUserResponse {

    /** UUIDs der Benutzer */
    #[Serializer\Type("array<string>")]
    public array $users;

    public function __construct(array $users) {
        $this->users = $users;
    }

}