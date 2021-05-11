<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Larapress\CRUD\Exceptions\AppException;

class SettingsDuplicateDomainProvider extends SettingsCRUDProvider
{
    public $createValidations = [
        'settings_id' => 'required|exists:settings,id',
        'domain_id' => 'required|exists:domains,id',
    ];

    /**
     * @param $args
     *
     * @return array|mixed
     * @throws AppException
     */
    public function onBeforeCreate($args)
    {
        $route = Route::current();
        $args['settings_id'] = $route->parameter('settings_id');
        $args['domain_id'] = $route->parameter('domain_id');

        /** @var \Larapress\Profiles\IProfileUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.master'))) {
            if (! in_array($args['domain_id'], $user->getAffiliateDomainIds())) {
                throw new AppException(AppException::ERR_OBJ_ACCESS_DENIED);
            }
        }

        return $args;
    }
}
