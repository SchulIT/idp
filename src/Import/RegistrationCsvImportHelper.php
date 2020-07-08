<?php

namespace App\Import;

use App\Entity\RegistrationCode;
use App\Entity\UserType;
use App\Repository\RegistrationCodeRepositoryInterface;
use League\Csv\Reader;

class RegistrationCsvImportHelper {

    private const FirstnameHeader = 'Vorname';
    private const LastnameHeader = 'Nachname';
    private const IdHeader = 'ID';
    private const EmailAddressHeader = 'E-Mail';
    private const CodeHeader = 'Code';
    private const GradeHeader = 'Klasse';
    private const UsernameHeader = 'Benutzername';
    private const SuffixHeader = 'Suffix';

    private $codeRepository;

    public function __construct(RegistrationCodeRepositoryInterface $codeRepository) {
        $this->codeRepository = $codeRepository;
    }

    public function getHeaders() {
        return [
            static::CodeHeader,
            static::UsernameHeader,
            static::SuffixHeader,
            static::FirstnameHeader,
            static::LastnameHeader,
            static::EmailAddressHeader,
            static::GradeHeader,
            static::IdHeader,
        ];
    }

    public function getRequiredHeaders() {
        return [
            static::CodeHeader
        ];
    }

    /**
     * Loads users from a given CSV file into a list of user objects
     *
     * @param string $csv
     * @param string $delimiter
     * @param UserType $userType
     * @return RegistrationCode[]
     * @throws RecordInvalidException
     * @throws \League\Csv\Exception
     */
    public function getCodesFromCsv(string $csv, string $delimiter, UserType $userType): array {
        $codes = [ ];

        $reader = Reader::createFromString($csv);
        $reader->setDelimiter($delimiter);
        $reader->setHeaderOffset(0);

        foreach ($reader->getRecords() as $offset => $record) {
            $firstname = $record[self::FirstnameHeader] ?? null;
            $lastname = $record[self::LastnameHeader] ?? null;
            $id = $record[self::IdHeader] ?? null;
            $email = $record[self::EmailAddressHeader] ?? null;
            $code = $record[self::CodeHeader] ?? null;
            $grade = $record[self::GradeHeader] ?? null;
            $username = $record[self::UsernameHeader] ?? null;
            $suffix = $record[self::SuffixHeader] ?? null;

            if(empty($code)) {
                throw new RecordInvalidException($offset, self::CodeHeader);
            }

            $registrationCode = $this->codeRepository->findOneByCode($code);

            if($registrationCode === null) {
                $registrationCode = (new RegistrationCode())
                    ->setCode($code);
            }

            if(!empty($firstname)) {
                $registrationCode->setFirstname($firstname);
            }

            if(!empty($lastname)) {
                $registrationCode->setLastname($lastname);
            }

            if(!empty($id)) {
                $registrationCode->setExternalId($id);
            }

            if(!empty($email)) {
                $registrationCode->setEmail($email);
            }

            if(!empty($grade)) {
                $registrationCode->setGrade($grade);
            }

            if(!empty($username)) {
                $registrationCode->setUsername($username);
            }

            if(!empty($suffix)) {
                if(substr($suffix, 0, 1) !== '@') {
                    $suffix = '@' . $suffix;
                }
                $registrationCode->setUsernameSuffix($suffix);
            }

            $codes[] = $registrationCode;
        }

        return $codes;
    }

}