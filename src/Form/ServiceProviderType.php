<?php

namespace App\Form;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ServiceProviderType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('url', UrlType::class, [
                'label' => 'label.url'
            ])
            ->add('entityId', UrlType::class, [
                'label' => 'label.entity_id'
            ])
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description'
            ])
            ->add('icon', FontAwesomeIconPicker::class, [
                'label' => 'label.icon.label',
                'help' => 'label.icon.help',
                'required' => false
            ])
            ->add('acs', UrlType::class, [
                'label' => 'label.acs'
            ])
            ->add('certificate', TextareaType::class, [
                'label' => 'label.certificate',
                'attr' => [
                    'rows' => 40
                ]
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();

            /** @var ServiceProvider $provider */
            $provider = $event->getData();

            if(!$provider instanceof SamlServiceProvider) {
                $form->remove('entityId')
                    ->remove('acs')
                    ->remove('certificate');
            }
        });
    }
}