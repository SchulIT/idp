<?php

namespace App\Invitation;

use App\Validator\Csv;
use Symfony\Component\Validator\Constraints as Assert;

class ImportInvitationEmailsRequest {
    #[Assert\NotBlank]
    #[Csv]
    public string|null $csv = null;

    #[Assert\NotBlank]
    public string $delimiter = ';';

    #[Assert\NotBlank]
    public string $emailHeader = 'E-Mail';

    #[Assert\NotBlank]
    public string $studentHeader = 'Schueler';

    public bool $createCodeIfNotExist = true;
}