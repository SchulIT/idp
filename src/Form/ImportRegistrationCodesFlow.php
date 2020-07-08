<?php

namespace App\Form;

use App\Import\ImportRegistrationCodeData;
use App\Import\ImportUserData;
use App\Import\RegistrationCsvImportHelper;
use Craue\FormFlowBundle\Form\FormFlow;

class ImportRegistrationCodesFlow extends FormFlow {
    protected $handleFileUploads = true;
    protected $revalidatePreviousSteps = false;

    private $helper;

    public function __construct(RegistrationCsvImportHelper $helper) {
        $this->helper = $helper;
    }

    public function loadStepsConfig() {
        return [
            [
                'label' => 'import.codes.step_one.label',
                'form_type' => ImportCsvType::class,
                'form_options' => [
                    'validation_groups' => ['step_one']
                ]
            ],
            [
                'label' => 'import.codes.step_two.label',
                'form_type' => ImportRegistrationCodesType::class,
                'form_options' => [
                    'validation_groups' => ['step_two', 'RegistrationCode']
                ]
            ]
        ];
    }

    public function getFormOptions($step, array $options = []) {
        $options = parent::getFormOptions($step, $options);

        if($step === 2) {
            /** @var ImportRegistrationCodeData $data */
            $data = $this->getFormData();
            $data->setCodes($this->helper->getCodesFromCsv(file_get_contents($data->getFile()->getPathname()), $data->getDelimiter(), $data->getUserType()));
        }

        return $options;
    }
}