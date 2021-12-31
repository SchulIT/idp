<?php

namespace App\Form;

use App\Import\ImportUserData;
use App\Import\RecordInvalidException;
use App\Import\UserCsvImportHelper;
use Craue\FormFlowBundle\Form\FormFlow;
use League\Csv\Exception as LeagueException;
use \Exception;

class ImportUsersFlow extends FormFlow {

    protected $handleFileUploads = true;
    protected $revalidatePreviousSteps = false;

    private $helper;

    public function __construct(UserCsvImportHelper $helper) {
        $this->helper = $helper;
    }

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

    public function getFormOptions($step, array $options = []): array {
        $options = parent::getFormOptions($step, $options);

        if($step === 2) {
            /** @var ImportUserData $data */
            $data = $this->getFormData();
            $data->setUsers($this->helper->getUsersFromCsv(file_get_contents($data->getFile()->getPathname()), $data->getDelimiter(), $data->getUserType()));
        }

        return $options;
    }
}