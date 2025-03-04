<?php

namespace App\Settings;

use App\Form\TextPrefixType;
use Jbtronics\SettingsBundle\ParameterTypes\StringType;
use Jbtronics\SettingsBundle\Settings\Settings;
use Jbtronics\SettingsBundle\Settings\SettingsParameter;
use Symfony\Component\Validator\Constraints as Assert;

#[Settings]
class RegistrationSettings {

    #[SettingsParameter(type: StringType::class, label: 'settings.registration.suffix.label', description: 'settings.registration.suffix.help', formType: TextPrefixType::class, formOptions: ['prefix' => '@'], nullable: false)]
    #[Assert\NotBlank]
    public string $usernameSuffix = 'e.schulit.de';
    
}