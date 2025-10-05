<?php

declare(strict_types=1);

namespace App\Import;

use League\Csv\Exception;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\UserRepositoryInterface;
use League\Csv\Reader;

class UserCsvImportHelper {

    private const string FirstnameHeader = 'Vorname';
    private const string LastnameHeader = 'Nachname';
    private const string IdHeader = 'ID';
    private const string EmailAddressHeader = 'E-Mail';
    private const string PasswordHeader = 'Passwort';
    private const string GradeHeader = 'Klasse';

    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function getHeaders(): array {
        return [
            self::EmailAddressHeader,
            self::PasswordHeader,
            self::FirstnameHeader,
            self::LastnameHeader,
            self::GradeHeader,
            self::IdHeader
        ];
    }

    public function getRequiredHeaders(): array {
        return [
            self::EmailAddressHeader
        ];
    }

    /**
     * Loads users from a given CSV file into a list of user objects
     *
     * @return User[]
     * @throws RecordInvalidException
     * @throws Exception
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
            $user = $this->userRepository->findOneByUsername($email);

            if(!$user instanceof User) {
                $user = new User();
                $user->setUsername($email);
                $user->setEmail($email);

                if(!empty($password)) {
                    $user->setPassword($password);
                    $user->setIsProvisioned(false);
                }
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

            if(!empty($grade)) {
                $user->setGrade($grade);
            }

            $user->setType($userType);

            $users[] = $user;
        }

        return $users;
    }
}
