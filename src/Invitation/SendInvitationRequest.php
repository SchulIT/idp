<?php

namespace App\Invitation;

use App\Entity\RegistrationCode;
use Symfony\Component\Validator\Constraints as Assert;

class SendInvitationRequest {
    #[Assert\NotBlank]
    #[Assert\Email]
    public string|null $email;

    function __construct(
        public RegistrationCode $code
    ) {

    }
}