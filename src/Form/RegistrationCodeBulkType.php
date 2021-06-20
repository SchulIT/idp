<?php

namespace App\Form;

use App\Repository\UserRepositoryInterface;
use App\Utils\ArrayUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationCodeBulkType extends AbstractType {
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $choices = ArrayUtils::createArrayWithKeys(
            $this->userRepository->findGrades(),
            function($grade) {
                return $grade;
            }
        );

        $builder
            ->add('grade', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'label.grade'
            ]);
    }
}