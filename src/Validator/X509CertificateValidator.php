<?php

declare(strict_types=1);

namespace App\Validator;

use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class X509CertificateValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint): void {
        if(!$constraint instanceof X509Certificate) {
            throw new InvalidArgumentException(
                sprintf('$constraint must be of type "%s" ("%s" given)', X509Certificate::class, $constraint::class)
            );
        }

        $resource = @openssl_x509_read($value);

        if($resource === false) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
