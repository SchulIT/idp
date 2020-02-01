<?php

namespace App\Import\User;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UserData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @Serializer\Type("boolean")
     */
    private $isActive = true;

    /**
     * @Serializer\Type("array<string, mixed>")
     */
    private $attributes = [ ];

    /**
     * @return mixed
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return UserData
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     * @return UserData
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return UserData
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     * @return UserData
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return UserData
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return UserData
     */
    public function setIsActive(bool $isActive): UserData {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return UserData
     */
    public function setAttributes(array $attributes): UserData {
        $this->attributes = $attributes;
        return $this;
    }
}