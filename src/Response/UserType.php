<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class UserType {
    public readonly string $uuid;

    public readonly string $name;

    public readonly string $alias;

    #[Serializer\Type("array<string>")]
    public readonly array $eduPersonAffiliation;

    /**
     * @param string $uuid
     * @param string $name
     * @param string $alias
     * @param string[] $eduPersonAffiliation
     */
    public function __construct(string $uuid, string $name, string $alias, array $eduPersonAffiliation) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->alias = $alias;
        $this->eduPersonAffiliation = $eduPersonAffiliation;
    }
}