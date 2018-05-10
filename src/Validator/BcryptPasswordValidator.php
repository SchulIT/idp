<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BcryptPasswordValidator extends ConstraintValidator {

    private $regExp = '^\$2y\$.{56}$';

    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof BcryptPassword) {
            throw new \InvalidArgumentException(
                sprintf('$constraint must be of type "%s" ("%s" given)', BcryptPassword::class, get_class($constraint))
            );
        }

        if(!preg_match($this->regExp, $value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}