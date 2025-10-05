<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jbtronics\SettingsBundle\Entity\AbstractSettingsORMEntry;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Setting extends AbstractSettingsORMEntry {
    use IdTrait;
}
