<?php

namespace App\Settings;

use Jbtronics\SettingsBundle\ParameterTypes\BoolType;
use Jbtronics\SettingsBundle\ParameterTypes\StringType;
use Jbtronics\SettingsBundle\Settings\Settings;
use Jbtronics\SettingsBundle\Settings\SettingsParameter;
use Jbtronics\SettingsBundle\Settings\SettingsTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[Settings]
class AppSettings {
    use SettingsTrait;

    #[SettingsParameter(type: StringType::class, label: 'settings.helpdesk.mail.label', description: 'settings.helpdesk.mail.help', formOptions: [ 'required' => false], nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    public ?string $helpdeskMail = null;

    #[SettingsParameter(type: BoolType::class, label: 'settings.security.password_compromised_check.label', description: 'settings.security.password_compromised_check.help', formOptions: [ 'required' => false], nullable: true)]

    public bool $isPasswordCompromisedCheckEnabled = true;
}