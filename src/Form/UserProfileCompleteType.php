<?php

namespace App\Form;

use App\Security\PasswordStrengthHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserProfileCompleteType extends AbstractType {

    private $passwordStrengthHelper;
    private $translator;

    public function __construct(PasswordStrengthHelper $passwordStrengthHelper, TranslatorInterface $translator) {
        $this->passwordStrengthHelper = $passwordStrengthHelper;
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver->setDefault('username_suffix', null)
            ->setDefault('can_edit_username', false);
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        if($options['username_suffix'] !== null) {
            $builder
                ->add('username', TextSuffixType::class, [
                    'label' => 'label.username',
                    'suffix' => $options['username_suffix']
                ]);
        } else {
            $builder
                ->add('username', TextType::class, [
                    'label' => 'label.username',
                    'disabled' => $options['can_edit_username'] === false
                ]);
        }

        $builder
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
            ])
            ->add('firstname', TextType::class, [
                'label' => 'label.firstname',
                'required' => false,
                'help' => 'help.voluntary'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'label.lastname',
                'required' => false,
                'help' => 'help.voluntary'
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'required' => false,
                'invalid_message' => $this->translator->trans('register.email.not_match', [], 'security'),
                'first_options' => [
                    'label' => 'label.email',
                    'help' => $this->translator->trans('register.complete.email_help', [], 'security')
                ],
                'second_options' => [
                    'label' => 'label.repeat_email'
                ]
            ]);
    }
}