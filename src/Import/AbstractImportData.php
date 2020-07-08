<?php

namespace App\Import;

use App\Entity\UserType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractImportData {
    /**
     * @Assert\File(groups={"step_one"})
     * @Assert\NotNull(groups={"step_one"})
     * @var File|null
     */
    private $file;

    /**
     * @Assert\NotNull(groups={"step_one"})
     * @Assert\Choice(choices={",", ";"}, groups={"step_one"})
     * @var string|null
     */
    private $delimiter;

    /**
     * @Assert\NotNull(groups={"step_one"})
     * @var UserType|null
     */
    private $userType;

    /**
     * @return File|null
     */
    public function getFile(): ?File {
        return $this->file;
    }

    /**
     * @param File|null $file
     * @return AbstractImportData
     */
    public function setFile(?File $file): AbstractImportData {
        $this->file = $file;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDelimiter(): ?string {
        return $this->delimiter;
    }

    /**
     * @param string|null $delimiter
     * @return AbstractImportData
     */
    public function setDelimiter(?string $delimiter): AbstractImportData {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * @return UserType|null
     */
    public function getUserType(): ?UserType {
        return $this->userType;
    }

    /**
     * @param UserType|null $userType
     * @return AbstractImportData
     */
    public function setUserType(?UserType $userType): AbstractImportData {
        $this->userType = $userType;
        return $this;
    }


}