<?php

namespace ServiceProvider;

use Checker\PhoneNumberValidator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;


class PhoneValidatorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['validator.phonenumber'] = function ($app) {
            $validator =  new PhoneNumberValidator();
            return $validator;
        };
    }
}