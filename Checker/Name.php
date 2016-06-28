<?php

namespace Checker;


use Symfony\Component\Validator\Constraint;

class Name extends Constraint
{
    public $message = 'Vardas negali turėti jokių ženklų ar skaičių!';
}