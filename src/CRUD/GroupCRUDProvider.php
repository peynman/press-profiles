<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\RBAC\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;
use Larapress\Profiles\Models\Group;

class GroupCRUDProvider implements ICRUDProvider
{
    use CRUDProviderTrait;

    public $name_in_config = 'larapress.profiles.routes.groups.name';
    public $model_in_config = 'larapress.profiles.routes.groups.model';
    public $compositions_in_config = 'larapress.profiles.routes.groups.compositions';

    public $verbs = [
        ICRUDVerb::VIEW,
        ICRUDVerb::SHOW,
        ICRUDVerb::CREATE,
        ICRUDVerb::EDIT,
        ICRUDVerb::DELETE,
    ];
    public $createValidations = [
    ];
    public $updateValidations = [
    ];
    public $validSortColumns = [
        'id',
        'name',
        'title',
        'created_at',
        'updated_at',
    ];
    public $searchColumns = [
        'name',
        'title',
    ];

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getValidRelations(): array
    {
        return [
            'permissions' => config('larapress.crud.routes.roles.provider'),
        ];
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function onBeforeQuery(Builder $query): Builder
    {
        /** @var ICRUDUser */
        $user = Auth::user();

        if (! $this->user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            $query->whereHas('owner', function ($q) use ($user) {
                $q->whereIn('id', $user->id);
            });
        }

        return $query;
    }

    /**
     * @param Group $object
     *
     * @return bool
     */
    public function onBeforeAccess($object): bool
    {
        /** @var ICRUDUser */
        $user = Auth::user();

        if (! $user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            return in_array($user->id, $object->getOwnerIdsAttribute());
        }

        return true;
    }
}
