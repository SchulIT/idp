<?php

namespace App\Import;

use App\Entity\User;
use App\Entity\UserType;
use App\Repository\UserRepositoryInterface;
use League\Csv\Reader;

class UserCsvImportHelper {

    private const FirstnameHeader = 'Vorname';
    private const LastnameHeader = 'Nachname';
    private const IdHeader = 'ID';
    private const EmailAddressHeader = 'E-Mail';
    private const PasswordHeader = 'Passwort';
    private const GradeHeader = 'Klasse';

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function getHeaders() {
        return [
            static::EmailAddressHeader,
            static::PasswordHeader,
            static::FirstnameHeader,
            static::LastnameHeader,
            static::GradeHeader,
            static::IdHeader
        ];
    }

    public function getRequiredHeaders() {
        return [
            static::EmailAddressHeader,
            static::PasswordHeader
        ];
    }

    /**
     * Loads users from a given CSV file into a list of user objects
     *
     * @param string $csv
     * @param string $delimiter
     * @param UserType $userType
     * @return User[]
     * @throws RecordInvalidException
     * @throws \League\Csv\Exception
     */
    public function getUsersFromCsv(string $csv, string $delimiter, UserType $userType): array {
        $reader = Reader::createFromString($csv);
        $reader->setDelimiter($delimiter);
        $reader->setHeaderOffset(0);

        $users = [ ];

        foreach ($reader->getRecords() as $offset => $record) {
            $firstname = $record[self::FirstnameHeader] ?? null;
            $lastname = $record[self::LastnameHeader] ?? null;
            $id = $record[self::IdHeader] ?? null;
            $email = $record[self::EmailAddressHeader] ?? null;
            $password = $record[self::PasswordHeader] ?? null;
            $grade = $record[self::GradeHeader] ?? null;

            if(empty($email)) {
                throw new RecordInvalidException($offset, self::EmailAddressHeader);
            }

            $user = null;

            if($user === null) {
                $user = $this->userRepository->findOneByUsername($email);
            }

            if($user === null) {
                if(empty($password)) {
                    throw new RecordInvalidException($offset, self::PasswordHeader);
                }

                $user = new User();
                $user->setUsername($email);
                $user->setEmail($email);
            }

            if(!empty($id)) {
                $user->setExternalId($id);
            }

            if(!empty($firstname)) {
                $user->setFirstname($firstname);
            }

            if(!empty($lastname)) {
                $user->setLastname($lastname);
            }

            if(!empty($password) && empty($user->getPassword())) {
                $user->setPassword($password);
            }

            if(!empty($grade)) {
                $user->setGrade($grade);
            }

            $user->setType($userType);

            $users[] = $user;
        }

        return $users;
    }
}