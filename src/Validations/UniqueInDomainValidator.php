<?php

namespace Larapress\Profiles\Validations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Larapress\Profiles\Models\Domain;

class UniqueInDomainValidator
{
    public static function register()
    {
        Validator::extend('domain_unique', function ($attribute, $value, $parameters) {
            $request = Request::createFromGlobals();
            $domain = Domain::select(['id'])->where('domain', $request->getHost())->first();
            $domain_id = null;
            if (! is_null($domain)) {
                $domain_id = $domain->id;
            }

            return DB::table($parameters[0])
                    ->where($parameters[1], $value)
                    ->where($parameters[2], $domain_id)
                    ->count() === 0;
        });
    }
}
