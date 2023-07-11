<?php

namespace App\Response;

readonly class ActiveDirectoryUser {

    public string $username;
    public string $firstname;
    public string $lastname;
    public ?string $grade;
    public string $guid;

    public function __construct(string $username,  string $firstname, string $lastname, ?string $grade, string $guid) {
        $this->username = $username;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->grade = $grade;
        $this->guid = $guid;
    }
}