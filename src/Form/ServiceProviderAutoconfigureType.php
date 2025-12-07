<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class ServiceProviderAutoconfigureType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('autoconfigureUrl', UrlType::class, [
                'label' => 'label.autoconfigure_url.label',
                'help' => 'label.autoconfigure_url.help',
                'required' => false
            ]);
    }
}