<?php

namespace App\Api\User;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UpdatedUsersRequestData {
    /**
     * @Serializer\Type("DateTime")
     * @Assert\NotNull()
     */
    public $updatedAfter;
}