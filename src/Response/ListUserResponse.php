<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ListUserResponse {
    /**
     * @param string[] $users
     */
    public function __construct(
        /**
         * List of objectGuids of all Active Directory users
         * @Serializer\SerializedName("users")
         * @Serializer\Type("array<string>")
         */
        private array $users
    )
    {
    }
}