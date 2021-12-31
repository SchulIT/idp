<?php

namespace App\Form;

use FervoEnumBundle\Generated\Form\ActiveDirectorySyncSourceTypeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ActiveDirectoryGradeSyncOptionType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('grade', TextType::class, [
                'label' => 'label.grade'
            ])
            ->add('sourceType', ActiveDirectorySyncSourceTypeType::class, [
                'label' => 'label.source',
                'label_attr' => [
                    'class' => 'radio-custom'
                ],
                'expanded' => true
            ])
            ->add('source', TextType::class, [
                'label' => 'label.value'
            ]);
    }
}