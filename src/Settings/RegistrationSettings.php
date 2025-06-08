<?php

namespace App\Settings;

use App\Form\TextCollectionEntryType;
use App\Form\TextPrefixType;
use Jbtronics\SettingsBundle\ParameterTypes\ArrayType;
use Jbtronics\SettingsBundle\ParameterTypes\StringType;
use Jbtronics\SettingsBundle\Settings\Settings;
use Jbtronics\SettingsBundle\Settings\SettingsParameter;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints as Assert;

#[Settings]
class RegistrationSettings {

    #[SettingsParameter(type: StringType::class, label: 'settings.registration.suffix.label', description: 'settings.registration.suffix.help', formType: TextPrefixType::class, formOptions: ['prefix' => '@'], nullable: false)]
    #[Assert\NotBlank]
    public string $usernameSuffix = 'e.schulit.de';

    #[SettingsParameter(type: ArrayType::class, label: 'settings.registration.email.disallowed_domains.label', description: 'settings.registration.email.disallowed_domains.help', options: ['type' => StringType::class, 'nullable' => false], nullable: false, formType: CollectionType::class, formOptions: [ 'entry_type' => TextCollectionEntryType::class, 'allow_add' => true, 'allow_delete' => true])]
    #[Assert\All(constraints: [ new Assert\NotBlank(), new Assert\Hostname() ])]
    #[Assert\Unique]
    public array $disallowedEmailDomains = [ ];
}