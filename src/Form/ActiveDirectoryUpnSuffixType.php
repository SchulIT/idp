<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ActiveDirectoryUpnSuffixType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('suffix', TextType::class, [
                'label' => 'label.suffix'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description',
                'required' => false
            ]);
    }
}