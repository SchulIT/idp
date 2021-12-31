<?php

namespace App\Form;

use App\Security\PasswordStrengthHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class PasswordChangeType extends AbstractType {

    private PasswordStrengthHelper $passwordStrengthHelper;

    public function __construct(PasswordStrengthHelper $passwordStrengthHelper) {
        $this->passwordStrengthHelper = $passwordStrengthHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'label.current_password',
                'constraints' => [
                    new UserPassword()
                ]
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => [
                    'attr' => [
                        'class' => 'password-field',
                        'autocomplete' => 'new-password'
                    ],
                ],
                'constraints' => $this->passwordStrengthHelper->getConstraints(),
                'first_options'  => ['label' => 'label.password'],
                'second_options' => ['label' => 'label.repeat_password']
            ]);
    }
}