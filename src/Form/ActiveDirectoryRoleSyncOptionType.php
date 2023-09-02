<?php

namespace App\Form;

use App\Entity\ActiveDirectorySyncSourceType;
use App\Entity\UserRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActiveDirectoryRoleSyncOptionType extends AbstractType {

    public function __construct(private readonly TranslatorInterface $translator) { }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description'
            ])
            ->add('sourceType', EnumType::class, [
                'class' => ActiveDirectorySyncSourceType::class,
                'label' => $this->translator->trans('label.source', [], 'enums'),
                'label_attr' => [
                    'class' => 'radio-custom'
                ],
                'expanded' => true,
                'choice_label' => fn(ActiveDirectorySyncSourceType $type) => $this->translator->trans('ad_source_type.' . $type->value, [], 'enums')
            ])
            ->add('source', TextType::class, [
                'label' => 'label.value'
            ])
            ->add('userRole', EntityType::class, [
                'class' => UserRole::class,
                'label' => 'label.user_role',
                'choice_label' => 'name'
            ]);
    }
}