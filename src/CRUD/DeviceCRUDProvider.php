<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Larapress\CRUD\Services\BaseCRUDProvider;
use Larapress\CRUD\Services\ICRUDProvider;
use Larapress\CRUD\Services\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\Flags\UserDomainFlags;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Device;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\DomainSub;

class DeviceCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.devices.name';
    public $verbs = [
        self::VIEW,
        self::DELETE,
    ];
    public $model = Device::class;
    public $searchColumns = [
        'domain',
        'id',
        'ips',
        'nameservers',
    ];
    public $validSortColumns = [
        'id',
        'user_id',
        'client_type',
        'client_agent',
        'client_ip',
        'created_at',
        'updated_at',
    ];
    public $validRelations = [
        'user',
        'domain'
    ];
    public $defaultShowRelations = [
        'user',
    ];

    /**
     * @param Builder $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var ICRUDUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * @param Domain $object
     *
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            return in_array($object->id, $user->getAffiliateDomainIds());
        }

        return true;
    }
}
