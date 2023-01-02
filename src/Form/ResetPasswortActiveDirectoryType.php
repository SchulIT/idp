<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ResetPasswortActiveDirectoryType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('admin_username', TextType::class, [
                'label' => 'users.reset_pw_ad.ad_user.label',
                'help' => 'users.reset_pw_ad.ad_user.help'
            ])
            ->add('admin_password', PasswordType::class, [
                'label' => 'users.reset_pw_ad.ad_password.label',
                'help' => 'users.reset_pw_ad.ad_password.help'
            ])
            ->add('username', TextType::class, [
                'label' => 'label.username',
                'disabled' => true
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => [
                    'attr' => [
                        'class' => 'password-field',
                        'autocomplete' => 'new-password'
                    ],
                ],
                'first_options'  => ['label' => 'label.password'],
                'second_options' => ['label' => 'label.repeat_password']
            ]);
    }
}