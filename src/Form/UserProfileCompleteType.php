<?php

namespace App\Form;

use App\Security\PasswordStrengthHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserProfileCompleteType extends AbstractType {

    private PasswordStrengthHelper $passwordStrengthHelper;
    private TranslatorInterface $translator;

    public function __construct(PasswordStrengthHelper $passwordStrengthHelper, TranslatorInterface $translator) {
        $this->passwordStrengthHelper = $passwordStrengthHelper;
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver->setRequired('username_suffix');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('username', TextSuffixType::class, [
                'label' => 'label.username',
                'suffix' => $options['username_suffix']
            ]);

        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'The password fields must match.',
                'options' => [
                    'attr' => [
                        'class' => 'password-field',
                        'autocomplete' => 'new-password'
                    ],
                ],
                'constraints' => $this->passwordStrengthHelper->getConstraints(),
                'first_options'  => [
                    'label' => 'label.password',
                    'help' => $this->translator->trans('password.requirements', [], 'security')
                ],
                'second_options' => ['label' => 'label.repeat_password']
            ])
            ->add('agreePrivacyPolicy', CheckboxType::class, [
                'required' => true,
                'label' => 'privacy_policy.accept',
                'constraints' => [
                    new IsTrue()
                ],
                'mapped' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
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