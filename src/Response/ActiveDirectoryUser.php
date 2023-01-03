<?php

namespace App\Response;

class ActiveDirectoryUser {

    public function __construct(private readonly string $username, private readonly string $firstname,
                                private readonly string $lastname, private readonly ?string $grade,
                                private readonly string $guid) {

    }

    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getFirstname(): string {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string {
        return $this->lastname;
    }

    /**
     * @return string|null
     */
    public function getGrade(): ?string {
        return $this->grade;
    }

    /**
     * @return string
     */
    public function getGuid(): string {
        return $this->guid;
    }
}