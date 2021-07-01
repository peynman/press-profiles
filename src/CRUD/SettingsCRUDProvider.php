<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\RBAC\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Settings;

class SettingsCRUDProvider implements ICRUDProvider
{
    use CRUDProviderTrait;

    public $name_in_config = 'larapress.profiles.routes.settings.name';
    public $model_in_config = 'larapress.profiles.routes.settings.model';
    public $compositions_in_config = 'larapress.profiles.routes.settings.compositions';

    public $verbs = [
        ICRUDVerb::VIEW,
        ICRUDVerb::CREATE,
        ICRUDVerb::EDIT,
        ICRUDVerb::DELETE,
    ];
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
     *
     * @return bool
     */
    public function onBeforeAccess($object): bool
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        if (! $user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            return $user->id === $object->author_id;
        }

        return true;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function onBeforeQuery(Builder $query): Builder
    {
        /** @var IProfileUser $user */
        $user = Auth::user();
        if (! $user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
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
     *
     * @return array
     */
    public function onBeforeCreate($args): array
    {
        $args['author_id'] = Auth::user()->id;
        return $args;
    }

    /**
     * Undocumented function
     *
     * @param array $args
     *
     * @return array
     */
    public function onBeforeUpdate($args): array
    {
        $args['author_id'] = Auth::user()->id;
        return $args;
    }

    /**
     * @param Settings $object
     * @param array  $input_data
     *
     * @return void
     */
    public function onAfterUpdate($object, array $input_data): void
    {
    }
}
