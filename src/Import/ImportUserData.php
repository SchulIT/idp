<?php

namespace App\Import;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class ImportUserData extends AbstractImportData {

    /**
     * @var User[]
     */
    #[Assert\Valid(groups: ['User', 'step_two'])]
    private array $users = [ ];

    /**
     * @return User[]
     */
    public function getUsers(): array {
        return $this->users;
    }

    /**
     * @param User[] $users
     */
    public function setUsers(array $users): ImportUserData {
        $this->users = $users;
        return $this;
    }
}