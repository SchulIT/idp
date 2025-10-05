<?php

declare(strict_types=1);

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

readonly class ListUserResponse {

    public function __construct(
        /** UUIDs der Benutzer */
        #[Serializer\Type("array<string>")]
        public array $users
    )
    {
    }

}
