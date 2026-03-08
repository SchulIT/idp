<?php

namespace App\Invitation;

use App\Entity\RegistrationCode;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\Registration\CodeGenerator;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use SchulIT\CommonBundle\Helper\DateHelper;

readonly class Importer {
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RegistrationCodeRepositoryInterface $codeRepository,
        private DateHelper $dateHelper,
        private CodeGenerator $codeGenerator
    ) {

    }

    /**
     * @throws Exception
     * @throws InvalidArgument
     */
    public function handle(ImportInvitationEmailsRequest $importRequest): void {
        $reader = Reader::fromString($importRequest->csv);
        $reader->setDelimiter($importRequest->delimiter);
        $reader->setHeaderOffset(0);

        $today = $this->dateHelper->getToday();

        foreach($reader->getRecords() as $record) {
            $parentEmail = $record[$importRequest->emailHeader];
            $studentUser = $this->userRepository->findOneByEmail($record[$importRequest->studentHeader]);

            /** @var RegistrationCode|null $code */
            $code = null;

            if($studentUser !== null) {
                $codes = $this->codeRepository->findAllByStudent($studentUser);
                foreach($codes as $codeEntity) {
                    if($codeEntity->getRedeemingUser() !== null
                        || $codeEntity->isDeleted() === true
                        || ($codeEntity->getValidFrom() !== null && $codeEntity->getValidFrom() > $today)) {
                        continue;
                    }

                    $code = $codeEntity;
                }

                if($code === null && $importRequest->createCodeIfNotExist) {
                    $codeEntity = (new RegistrationCode())
                        ->setStudent($studentUser)
                        ->setCode($this->codeGenerator->generateCode());

                    $code = $codeEntity;
                }
            }

            if($code !== null) {
                $code->setInvitationEmail($parentEmail);
                $code->setInvitationSentAt(null);
                $this->codeRepository->persist($code);
            }
        }
    }
}