<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ActiveDirectoryUser extends User {
    /**
     * @ORM\Column(type="string", length=191, unique=true)
     */
    private $samAccountName;

    /**
     * @param string $samAccountName
     * @return ActiveDirectoryUser
     */
    public function setSamAccountName($samAccountName) {
        $this->samAccountName = $samAccountName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSamAccountName() {
        return $this->samAccountName;
    }
}