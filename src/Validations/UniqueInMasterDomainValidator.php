<?php


namespace Larapress\Profiles\Validations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Larapress\Core\Exceptions\AppException;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;

class UniqueInMasterDomainValidator
{
    public static function register()
    {
        Validator::extend('master_domain_unique', function ($attribute, $value, $parameters) {
            $request = Request::createFromGlobals();
            /** @var Domain $domain */
            $domain = Domain::select(['id'])->where('domain', $request->getHost())->first();
            $domain_ids = null;
            if (!is_null($domain)) {
                $domain_ids = [];

                /** @var IProfileUser[] $master_aff */
                $master_aff = $domain->users()->whereHas('roles', function ($q) {
                    $q->where('name', config(''));
                })->get();

                if (count($master_aff) == 1) {
                    $domain_ids = $master_aff[0]->getAffiliateDomainIds();
                } elseif (count($master_aff) > 1) {
                    throw new AppException(AppException::ERR_INVALID_CONFIG_DOMAIN);
                }
            }

            if (!is_null($domain_ids)) {
                return DB::table($parameters[0])
                        ->where($parameters[1], $value)
                        ->whereIn($parameters[2], $domain_ids)
                        ->count() === 0;
            }

            return DB::table($parameters[0])
                    ->where($parameters[1], $value)
                    ->count() === 0;
        });
    }
}
