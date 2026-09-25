<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleChoiceType extends ChoiceType {
    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'idp.roles.user' => 'ROLE_USER',
                'idp.roles.password_manager' => 'ROLE_PASSWORD_MANAGER',
                'idp.roles.admin' => 'ROLE_ADMIN',
                'idp.roles.super_admin' => 'ROLE_SUPER_ADMIN'
            ],
            'multiple' => true,
            'expanded' => true,
            'label' => 'label.roles.label',
            'help' => 'label.roles.help',
            'empty_data' => [ ]
        ]);
    }
}
