<?php

namespace App\Settings;

use App\Form\MarkdownType;
use Jbtronics\SettingsBundle\ParameterTypes\StringType;
use Jbtronics\SettingsBundle\Settings\Settings;
use Jbtronics\SettingsBundle\Settings\SettingsParameter;
use Jbtronics\SettingsBundle\Settings\SettingsTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[Settings]
class InvitationSettings {
    use SettingsTrait;

    #[SettingsParameter(type: StringType::class, label: 'settings.invitation.subject.label', description: 'settings.invitation.subject.help', nullable: true)]
    #[Assert\NotBlank]
    public string|null $subject = null;

    #[SettingsParameter(type: StringType::class, label: 'settings.invitation.message.label', description: 'settings.invitation.message.help', formType: MarkdownType::class, formOptions: [ 'required' => false], nullable: true)]
    #[Assert\NotBlank]
    public string|null $message = null;

    #[SettingsParameter(type: StringType::class, label: 'settings.invitation.return_address.label', description: 'settings.invitation.return_address.help', nullable: true)]
    #[Assert\NotBlank]
    public string|null $returnAddress = null;


}