<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Import\ImportUserData;
use App\Import\RecordInvalidException;
use App\Import\UserCsvImportHelper;
use App\Repository\UserRepositoryInterface;
use Craue\FormFlowBundle\Form\FormFlow;
use League\Csv\Exception as LeagueException;
use \Exception;
use Override;

class ImportUsersFlow extends FormFlow {

    protected $handleFileUploads = true;
    protected $revalidatePreviousSteps = false;

    public function __construct(private readonly UserCsvImportHelper $helper, private readonly UserRepositoryInterface $userRepository)
    {
    }

    #[Override]
    public function loadStepsConfig(): array {
        return [
            [
                'label' => 'import.users.step_one.label',
                'form_type' => ImportCsvType::class,
                'form_options' => [
                    'validation_groups' => ['step_one']
                ]
            ],
            [
                'label' => 'import.users.step_two.label',
                'form_type' => ImportUsersType::class,
                'form_options' => [
                    'validation_groups' => ['step_two', 'User']
                ]
            ]
        ];
    }

    #[Override]
    public function getFormOptions($step, array $options = []): array {
        $options = parent::getFormOptions($step, $options);

        if($step === 2) {
            /** @var ImportUserData $data */
            $data = $this->getFormData();

            $addOrUpdate = $this->helper->getUsersFromCsv(file_get_contents($data->getFile()->getPathname()), $data->getDelimiter(), $data->getUserType());
            $data->setUsers($addOrUpdate);

            // Compute removal (if necessary)
            if($data->isPerformSync()) {
                $usernames = array_map(fn(User $user): string => $user->getUsername(), $addOrUpdate);
                $toDelete = $this->userRepository->findAllNotInUsernamesList($usernames, $data->getUserType());
                $data->setRemoveUsers($toDelete);
            }
        }

        return $options;
    }
}
