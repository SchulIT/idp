<?php

declare(strict_types=1);

namespace App\Response;

readonly class ActiveDirectoryUser {

    public function __construct(public string $username, public string $firstname, public string $lastname, public ?string $grade, public string $guid)
    {
    }
}
