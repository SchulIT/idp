<?php

namespace App\Response;

use App\Entity\UserType;
use JMS\Serializer\Annotation as Serializer;

class ListUserTypeResponse {

    /**
     * @param UserType[] $types
     */
    public function __construct(
        /**
         * @Serializer\SerializedName("types")
         * @Serializer\Type("array<App\Entity\UserType>")
         */
        private array $types
    )
    {
    }
}