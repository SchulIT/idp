<?php

namespace App\Autoconfig;

use Symfony\Component\Validator\Constraints as Assert;

class AutoconfigService {

    #[Assert\NotBlank]
    #[Assert\Url(protocols: ['https'])]
    public string|null $autoconfigureUrl = null;
}