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
     * @var User[]
     */
    private array $removeUsers = [ ];

    private bool $performSync = false;

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

    /**
     * @return array
     */
    public function getRemoveUsers(): array {
        return $this->removeUsers;
    }

    /**
     * @param array $removeUsers
     */
    public function setRemoveUsers(array $removeUsers): void {
        $this->removeUsers = $removeUsers;
    }

    /**
     * @return bool
     */
    public function isPerformSync(): bool {
        return $this->performSync;
    }

    /**
     * @param bool $performSync
     */
    public function setPerformSync(bool $performSync): void {
        $this->performSync = $performSync;
    }
}