<?php

namespace Checker;

use Symfony\Component\Validator\Constraint;

class PhoneNumber extends Constraint
{
    public $message = 'Numeris "%string%" nėra tinkamas. Gali būti panaudoti tik skaičiai, tarpai ir pliuso ženklas +';
}