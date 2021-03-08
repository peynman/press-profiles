<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Services\BaseCRUDProvider;
use Larapress\CRUD\Services\ICRUDProvider;
use Larapress\CRUD\Services\IPermissionsMetadata;
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
        'val' => 'required',
        'user_id' => 'nullable|numeric|exists:users,id',
        'domain_id' => 'nullable|numeric|exists:domains,id',
        'type' => 'nullable|string',
    ];
    public $updateValidations = [
        'key' => 'required|string',
        'val' => 'required',
        'user_id' => 'nullable|numeric|exists:users,id',
        'domain_id' => 'nullable|numeric|exists:domains,id',
        'type' => 'nullable|string',
    ];
    public $validSortColumns = [
        'id',
        'key',
        'val',
        'type',
        'user_id',
        'author_id',
        'created_at',
        'updated_at'
    ];
    public $validRelations = [
        'user',
        'author',
        'domains'
    ];
    public $searchColumns = [
        'val',
        'key'
    ];
    public $filterFields = [
        'type' => 'equals:type',
        'domains' => 'has:domains,id',
        'user_id' => 'equals:user_id',
        'author_id' => 'equals:author_id',
    ];

    /**
     * @param Settings $object
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        if (! $user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            return $user->id === $object->author_id;
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

        if (! $user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            $query
            ->whereHas('domains', function ($q) use ($user) {
                $q->whereIn('id', $user->getAffiliateDomainIds());
            })
            ->orWhere('author_id', $user->id);
        }

        return $query;
    }

    /**
     * Undocumented function
     *
     * @param array $args
     * @return array
     */
    public function onBeforeCreate($args)
    {
        $args['author_id'] = Auth::user()->id;
        return $args;
    }

    /**
     * Undocumented function
     *
     * @param array $args
     * @return array
     */
    public function onBeforeUpdate($args)
    {
        $args['author_id'] = Auth::user()->id;
        return $args;
    }

    /**
     * @param Settings $object
     * @param array  $input_data
     *
     * @return object
     */
    public function onAfterUpdate($object, $input_data)
    {
        return $object;
    }
}
