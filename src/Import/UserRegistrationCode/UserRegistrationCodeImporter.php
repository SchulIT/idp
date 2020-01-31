<?php

namespace App\Import\UserRegistrationCode;

use App\Entity\UserRegistrationCode;
use App\Entity\UserType;
use App\Import\AbstractImporter;
use App\Import\FailedImportResult;
use App\Repository\UserRegistrationCodeRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRegistrationCodeImporter extends AbstractImporter {

    private $repository;
    private $userTypeRepository;

    public function __construct(UserRegistrationCodeRepositoryInterface $repository, UserTypeRepositoryInterface $userTypeRepository, SerializerInterface $serializer, ValidatorInterface $validator, LoggerInterface $logger = null) {
        parent::__construct($serializer, $validator, $logger);

        $this->repository = $repository;
        $this->userTypeRepository = $userTypeRepository;
    }

    public function import($json) {
        /** @var UserRegistrationCodesData $codeImportData */
        $codeImportData = $this->parseJson($json, UserRegistrationCodesData::class);

        /** @var UserType[] $types */
        $types = $this->userTypeRepository
            ->findAll();

        /** @var UserRegistrationCode[] $added */
        $added = [ ];
        /** @var UserRegistrationCode[] $updated */
        $updated = [ ];

        try {
            $this->repository->beginTransaction();

            foreach($codeImportData->getCodes() as $codeData) {
                $code = $this->repository->findOneByCode($codeData->getCode());

                if($code === null) {
                    $code = (new UserRegistrationCode())
                        ->setCode($codeData->getCode());

                    $added[] = $code;
                } else if($code->getRedeemedAt() !== null) {
                    // Code was already redeemed -> do not update
                    continue;
                } else {
                    $updated[] = $code;
                }

                $code->setFirstname($codeData->getFirstname());
                $code->setLastname($codeData->getLastname());
                $code->setEmail($codeData->getEmail());
                $code->setGrade($codeData->getGrade());
                $code->setInternalId($codeData->getInternalId());

                foreach($types as $type) {
                    if($type->getAlias() === $codeData->getType()) {
                        $code->setType($type);
                    }
                }

                $this->repository->persist($code);
            }

            $this->repository->commit();

            return new UserRegistrationCodeImportResult(true, $added, $updated);
        } catch(\Exception $exception) {
            $this->repository->rollBack();

            return new FailedImportResult($exception);
        }
    }
}