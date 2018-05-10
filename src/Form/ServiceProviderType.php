<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class ServiceProviderType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('entityId', UrlType::class, [
                'label' => 'label.entity_id'
            ])
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description'
            ])
            ->add('acs', UrlType::class, [
                'label' => 'label.acs'
            ])
            ->add('url', UrlType::class, [
                'label' => 'label.url'
            ])
            ->add('certificate', TextareaType::class, [
                'label' => 'label.certificate',
                'attr' => [
                    'rows' => 40
                ]
            ]);
    }
}