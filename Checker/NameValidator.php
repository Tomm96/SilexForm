<?php

namespace Checker;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NameValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        if (!ctype_alpha($value)) {

            $this->context->addViolation(
                $constraint->message,
                array('%string%' => $value)
            );

            return false;
        }

        return true;
    }
}