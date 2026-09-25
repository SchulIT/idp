<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ActiveDirectorySyncSourceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActiveDirectoryGradeSyncOptionType extends AbstractType {

    public function __construct(private readonly TranslatorInterface $translator) { }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('grade', TextType::class, [
                'label' => 'label.grade'
            ])
            ->add('sourceType', EnumType::class, [
                'class' => ActiveDirectorySyncSourceType::class,
                'label' => $this->translator->trans('label.source', [], 'enums'),
                'expanded' => true,
            ])
            ->add('source', TextType::class, [
                'label' => 'label.value'
            ]);
    }
}
