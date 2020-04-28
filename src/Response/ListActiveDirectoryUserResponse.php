<?php

namespace App\Response;

class ListActiveDirectoryUserResponse {

    /**
     * List of objectGuids of all Active Directory users
     * @var string[]
     */
    private $users = [ ];

    /**
     * @param string[] $users
     */
    public function __construct(array $users) {
        $this->users = $users;
    }
}