<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ListActiveDirectoryUserResponse {

    /**
     * @param string[] $users
     */
    public function __construct(
        /**
         * List of objectGuids of all Active Directory users
         *
         * @Serializer\Type("array<string>")
         * @Serializer\SerializedName("users")
         */
        private array $users
    )
    {
    }
}