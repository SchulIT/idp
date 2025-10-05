<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\UserType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractImportData {
    #[Assert\File(groups: ['step_one'])]
    #[Assert\NotNull(groups: ['step_one'])]
    private ?File $file = null;

    #[Assert\NotNull(groups: ['step_one'])]
    #[Assert\Choice(choices: [',', ';'], groups: ['step_one'])]
    private ?string $delimiter = null;

    #[Assert\NotNull(groups: ['step_one'])]
    private ?UserType $userType = null;

    public function getFile(): ?File {
        return $this->file;
    }

    public function setFile(?File $file): AbstractImportData {
        $this->file = $file;
        return $this;
    }

    public function getDelimiter(): ?string {
        return $this->delimiter;
    }

    public function setDelimiter(?string $delimiter): AbstractImportData {
        $this->delimiter = $delimiter;
        return $this;
    }

    public function getUserType(): ?UserType {
        return $this->userType;
    }

    public function setUserType(?UserType $userType): AbstractImportData {
        $this->userType = $userType;
        return $this;
    }


}
