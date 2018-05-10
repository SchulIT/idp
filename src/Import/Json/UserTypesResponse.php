<?php

namespace App\Import\Json;

use App\Entity\UserType;
use JMS\Serializer\Annotation as Serializer;

class UserTypesResponse extends Response {

    /**
     * @Serializer\Accessor(getter="getTypes")
     * @var UserType[]
     */
    private $types;

    /**
     * UserTypesResponse constructor.
     * @param $isSuccessful
     * @param UserType[] $types
     */
    public function __construct($isSuccessful, array $types) {
        parent::__construct($isSuccessful);

        $this->types = $types;
    }

    public function getTypes() {
        return $this->types;
    }
}