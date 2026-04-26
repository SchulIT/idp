<?php

namespace App\Validator;

use App\Invitation\ImportInvitationEmailsRequest;
use League\Csv\Reader;
use Override;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Throwable;

class CsvValidator extends ConstraintValidator {

    #[Override]
    public function validate(mixed $value, Constraint $constraint): void {
        if(!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if(!$constraint instanceof Csv) {
            throw new UnexpectedValueException($constraint, Csv::class);
        }

        if(empty($value)) {
            return;
        }

        /** @var ImportInvitationEmailsRequest $csv */
        $csv = $this->context->getObject();

        try {
            $reader = Reader::fromString($value);

            $reader->setDelimiter($csv->delimiter);
            $reader->setHeaderOffset(0);

            foreach($reader as $row) {
                $_ = $row[$csv->emailHeader];
                $_ = $row[$csv->studentHeader];
            }
        } catch (Throwable $e) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('%error%', $e->getMessage())
                ->addViolation();
        }
    }
}