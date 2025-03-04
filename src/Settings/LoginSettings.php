<?php

namespace App\Settings;

use App\Form\MarkdownType;
use Jbtronics\SettingsBundle\ParameterTypes\StringType;
use Jbtronics\SettingsBundle\Settings\Settings;
use Jbtronics\SettingsBundle\Settings\SettingsParameter;
use Symfony\Component\Validator\Constraints as Assert;

#[Settings]
class LoginSettings {

    #[SettingsParameter(type: StringType::class, label: 'settings.login.message.label', description: 'settings.login.message.help', formType: MarkdownType::class, formOptions: [ 'required' => false], nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    public ?string $loginMessage = null;
}