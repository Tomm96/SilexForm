<?php

namespace Checker;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
//        if (!preg_match('/^[a-zA-Za0-9]+$/', $value, $matches)) {
//            $this->context->addViolation(
//                $constraint->message,
//                array('%string%' => $value)
//            );
//            return false;
//        }

        if($value == "" || $this->isPhoneNumber($value)) {
            return true;
        }

        $this->context->addViolation(
                $constraint->message,
                array('%string%' => $value)
        );
        return false;
    }

    private function isPhoneNumber($value) {
        $chars = str_split($value);

        foreach ($chars as $char) {
            if (!is_numeric($char) && $char != "+" && $char != " ") return false;
        }

        return true;
    }
}