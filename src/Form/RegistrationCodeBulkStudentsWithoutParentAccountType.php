<?php

namespace App\Form;

use App\Converter\UserStringConverter;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Utils\ArrayUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationCodeBulkStudentsWithoutParentAccountType extends AbstractType {
    private $userRepository;
    private $userConverter;

    public function __construct(UserRepositoryInterface $userRepository, UserStringConverter $userConverter) {
        $this->userRepository = $userRepository;
        $this->userConverter = $userConverter;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $choices = ArrayUtils::createArrayWithKeys(
            $this->userRepository->findAllStudentsWithoutParents(),
            function(User $user) {
                return $this->userConverter->convert($user);
            });

        $builder
            ->add('students', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'label.students_simple',
                'multiple' => true,
                'attr' => [
                    'data-choice' => 'true'
                ],
                'data' => $choices
            ])
            ->add('validFrom', DateType::class, [
                'label' => 'label.enabled_from',
                'required' => false,
                'widget' => 'single_text'
            ]);
    }
}