<?php

namespace App\Form;

use App\Import\ImportRegistrationCodeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportRegistrationCodesType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('codes', CollectionType::class, [
                'entry_type' => ImportRegistrationCodeType::class,
                'entry_options' => [
                    'label' => false
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('data_class', ImportRegistrationCodeData::class);
    }
}