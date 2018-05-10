<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
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