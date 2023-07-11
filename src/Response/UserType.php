<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

readonly class UserType {
    public string $uuid;

    public string $name;

    public string $alias;

    #[Serializer\Type("array<string>")]
    public array $eduPersonAffiliation;

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