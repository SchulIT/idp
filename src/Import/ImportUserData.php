<?php

namespace App\Import;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class ImportUserData extends AbstractImportData {

    /**
     * @Assert\Valid(groups={"User", "step_two"})
     * @var User[]
     */
    private $users = [ ];

    /**
     * @return User[]
     */
    public function getUsers(): array {
        return $this->users;
    }

    /**
     * @param User[] $users
     * @return ImportUserData
     */
    public function setUsers(array $users): ImportUserData {
        $this->users = $users;
        return $this;
    }
}