<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

readonly class UserType {
    /**
     * @param string $uuid
     * @param string $name
     * @param string $alias
     * @param string[] $eduPersonAffiliation
     */
    public function __construct(public string $uuid, public string $name, public string $alias, #[Serializer\Type("array<string>")]
    public array $eduPersonAffiliation)
    {
    }
}