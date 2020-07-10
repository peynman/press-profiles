<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\Base\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\Models\PhoneNumber;
use Larapress\Profiles\IProfileUser;

class PhoneNumberCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.phone-numbers.name';
    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
        self::REPORTS,
    ];
    public $model = PhoneNumber::class;
    public $createValidations = [
        'user_id' => 'required|numeric|exists:users,id',
        'number' => 'required|numeric_farsi|unique:phone_numbers,number',
        'flags' => 'numeric',
        'data' => 'nullable|json',
    ];
    public $updateValidations = [
        'user_id' => 'required|numeric|exists:users,id',
        'number' => 'required|numeric_farsi|unique:phone_numbers,number',
        'flags' => 'numeric',
        'data' => 'nullable|json',
    ];
    public $autoSyncRelations = [
    ];
    public $validSortColumns = [
        'id',
        'number',
        'created_at',
        'updated_at',
        'flags',
        'domain_id',
        'user_id',
    ];
    public $validRelations = [
        'user',
        'domain'
    ];
    public $validFilters = [];
    public $defaultShowRelations = [
        'user',
        'domain'
    ];
    public $excludeIfNull = [];
    public $searchColumns = [
        'number'
    ];
    public $filterDefaults = [];
    public $filterFields = [];

    /**
     * @param Builder $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            $query->whereIn('domain_id', $user->getAffiliateDomainIds());
        }

        return $query;
    }

    /**
     * @param PhoneNumber $object
     *
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            return in_array($object->domain_id, $user->getAffiliateDomainIds());
        }

        return true;
    }

}
