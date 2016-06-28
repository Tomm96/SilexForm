<?php

namespace ServiceProvider;

use Checker\NameValidator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class NameValidatorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['validator.name'] = function ($app) {
            $validator =  new NameValidator();
            return $validator;
        };
    }
}