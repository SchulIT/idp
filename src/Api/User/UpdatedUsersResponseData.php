<?php

namespace App\Api\User;

use JMS\Serializer\Annotation as Serializer;

class UpdatedUsersResponseData {
    /**
     * @Serializer\Type("array<int>")
     */
    public $userIds = [ ];
}