<?php

namespace App\Form;

use App\Import\ImportUserData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportUsersType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('users', CollectionType::class, [
                'entry_type' => ImportUserType::class,
                'entry_options' => [
                    'label' => false
                ]
            ])
            ->add('removeUsers', CollectionType::class, [
                'entry_type' => UserUsernameGradeType::class,
                'entry_options' => [
                    'label' => false
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', ImportUserData::class);
    }
}