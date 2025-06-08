<?php

namespace App\Validator;

use App\Settings\RegistrationSettings;
use Stringable;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use function is_scalar;

class AllowedEmailDomainValidator extends ConstraintValidator {

    public function __construct(private readonly RegistrationSettings $registrationSettings) {

    }

    public function validate(mixed $value, Constraint $constraint): void {
        if (!is_scalar($value) && !$value instanceof Stringable) {
            throw new UnexpectedValueException($value, 'string');
        }

        if(!$constraint instanceof AllowedEmailDomain) {
            throw new UnexpectedTypeException($constraint, AllowedEmailDomain::class);
        }

        if(empty($value)) {
            return;
        }

        $parts = explode('@', $value);
        $domain = $parts[count($parts) - 1];

        if(in_array($domain, $this->registrationSettings->disallowedEmailDomains)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }

}