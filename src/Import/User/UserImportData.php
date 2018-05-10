<?php

namespace App\Import\User;

use JMS\Serializer\Annotation as Serializer;

class UserImportData {
    /**
     * @Serializer\Type("array<App\Import\User\UserData>")
     * @Serializer\Accessor(getter="getUsers", setter="setUsers")
     */
    private $users;

    public function setUsers(array $users) {
        $this->users = $users;
    }

    /**
     * @return UserData[]
     */
    public function getUsers() {
        return $this->users;
    }
}