<?php

namespace App\Form;

use App\Repository\UserRepositoryInterface;
use App\Utils\ArrayUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationCodeBulkType extends AbstractType {
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $choices = ArrayUtils::createArrayWithKeys(
            $this->userRepository->findGrades(),
            fn($grade) => $grade
        );

        $builder
            ->add('grade', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'label.grade'
            ])
            ->add('validFrom', DateType::class, [
                'label' => 'label.enabled_from',
                'required' => false,
                'widget' => 'single_text'
            ]);
    }
}