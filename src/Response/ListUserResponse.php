<?php

namespace App\Response;

class ListUserResponse {
    /**
     * @param string[] $users
     */
    public function __construct(
        /**
         * List of objectGuids of all Active Directory users
         */
        private readonly array $users
    )
    {
    }

    /**
     * @return string[]
     */
    public function getUsers(): array {
        return $this->users;
    }
}