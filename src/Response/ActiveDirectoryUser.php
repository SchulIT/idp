<?php

namespace App\Response;

class ActiveDirectoryUser {

    public readonly string $username;
    public readonly string $firstname;
    public readonly string $lastname;
    public readonly ?string $grade;
    public readonly string $guid;

    public function __construct(string $username,  string $firstname, string $lastname, ?string $grade, string $guid) {
        $this->username = $username;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->grade = $grade;
        $this->guid = $guid;
    }
}