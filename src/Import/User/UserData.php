<?php

namespace App\Import\User;

use App\Validator\BcryptPassword;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UserData {
    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     */
    public $username;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @BcryptPassword()
     */
    public $password;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     */
    public $firstname;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     */
    public $lastname;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     */
    public $type;

    /**
     * @Serializer\Type("boolean")
     */
    public $isActive = true;

    /**
     * @Serializer\Type("array<string, mixed>")
     */
    public $attributes = [ ];
}