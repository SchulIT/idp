<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('certificate', TextareaType::class, [
                'label' => 'label.certificate',
                'attr' => [
                    'rows' => 40
                ]
            ])
            ->add('acsUrls', CollectionType::class, [
                'label' => 'label.acs',
                'entry_type' => TextCollectionEntryType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('attributeNameMapping', KeyValueType::class, [
                'label' => 'label.attribute_name_mapping.label',
                'help' =>  'label.attribute_name_mapping.help',
                'value_type' => TextType::class,
                'value_options' => [
                    'label' => 'label.mapped_attribute_name'
                ],
                'key_type' => TextType::class,
                'key_options' => [
                    'label' => 'label.original_attribute_name'
                ],
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('autoconfigureUrl', UrlType::class, [
                'label' => 'label.autoconfigure_url.label',
                'help' => 'label.autoconfigure_url.help',
                'required' => false
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event): void {
            $form = $event->getForm();

            /** @var ServiceProvider $provider */
            $provider = $event->getData();

            if(!$provider instanceof SamlServiceProvider) {
                $form->remove('entityId')
                    ->remove('acsUrls')
                    ->remove('certificate')
                    ->remove('attributeNameMapping')
                    ->remove('autoconfigureUrl');
            }
        });
    }
}
