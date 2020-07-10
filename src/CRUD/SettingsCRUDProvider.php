<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\Base\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Settings;

class SettingsCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.settings.name';
    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
    ];
    public $model = Settings::class;
    public $createValidations = [
        'key' => 'required|string',
        'val' => 'required|string',
        'user_id' => 'nullable|numeric|exists:users,id',
        'type' => 'nullable|string',
        'domain_id' => 'nullable|numeric|exists:domains,id',
    ];
    public $updateValidations = [
        'key' => 'required|string',
        'val' => 'required|string',
        'user_id' => 'nullable|numeric|exists:users,id',
        'type' => 'nullable|string',
        'domain_id' => 'nullable|numeric|exists:domains,id',
    ];
    public $autoSyncRelations = [];
    public $validSortColumns = ['id', 'key', 'val', 'type', 'user_id', 'domain_id', 'created_at'];
    public $validRelations = ['user', 'domain'];
    public $validFilters = [];
    public $defaultShowRelations = ['user', 'domain'];
    public $excludeIfNull = [];
    public $searchColumns = ['val', 'key'];
    public $filterDefaults = [
        'sub_domain' => null,
        'user_id' => null,
        'type' => null,
    ];
    public $filterFields = [
        'type' => 'equals:type',
        'domain' => 'equals:domain_id',
        'user_id' => 'equals:user_id',
    ];

    /**
     * @param Settings $object
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            return in_array($object->domain_id, $user->getAffiliateDomainIds());
        }

        return true;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            $query->whereIn('domain_id', $user->getAffiliateDomainIds());
        }

        return $query;
    }

    public function onBeforeCreate($args)
    {
        return $args;
    }

    public function onBeforeUpdate($args)
    {
        return $this->onBeforeCreate($args);
    }

    /**
     * @param Settings $object
     * @param array  $input_data
     *
     * @return object
     */
    public function onAfterUpdate($object, $input_data)
    {
        Settings::forgetFromCache($object->key, $object->user_id);

        return $object;
    }
}
