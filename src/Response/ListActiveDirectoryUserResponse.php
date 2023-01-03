<?php

namespace App\Response;

class ListActiveDirectoryUserResponse {
    public function __construct(private readonly array $users) { }

    /**
     * @return ActiveDirectoryUser[]
     */
    public function getUsers(): array {
        return $this->users;
    }
}