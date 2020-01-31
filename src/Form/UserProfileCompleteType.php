<?php

namespace App\Form;

use App\Security\PasswordStrengthHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserProfileCompleteType extends AbstractType {

    private $passwordStrengthHelper;
    private $translator;

    public function __construct(PasswordStrengthHelper $passwordStrengthHelper, TranslatorInterface $translator) {
        $this->passwordStrengthHelper = $passwordStrengthHelper;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'label.firstname'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'label.lastname'
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'invalid_message' => $this->translator->trans('register.email.not_match', [], 'security'),
                'first_options' => [
                    'label' => 'label.email'
                ],
                'second_options' => [
                    'label' => 'label.repeat_email'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'The password fields must match.',
                'options' => [
                    'attr' => [
                        'class' => 'password-field'
                    ],
                ],
                'constraints' => $this->passwordStrengthHelper->getConstraints(),
                'first_options'  => [
                    'label' => 'label.password',
                    'help' => $this->translator->trans('password.requirements', [], 'security')
                ],
                'second_options' => ['label' => 'label.repeat_password']
            ]);
    }
}