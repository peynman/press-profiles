<?php

namespace Larapress\Profiles\Validations;

use Illuminate\Support\Facades\Validator;

class DomainValidator
{
    public static function register()
    {
        Validator::extend('domain', function ($attributes, $values, $parameters, $validator) {
            return preg_match(
                "/^([a-zA-Z0-9][a-zA-Z0-9-_]*\.)*[a-zA-Z0-9]*[a-zA-Z0-9-_]*[[a-zA-Z0-9]+$/",
                $values
            );
        });
    }
}
