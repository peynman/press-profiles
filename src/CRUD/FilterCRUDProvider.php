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
use Larapress\Profiles\Models\Filter;

class FilterCRUDProvider implements ICRUDProvider
{
    use CRUDProviderTrait;

    public $name_in_config = 'larapress.profiles.routes.filters.name';
    public $model_in_config = 'larapress.profiles.routes.filters.model';
    public $compositions_in_config = 'larapress.profiles.routes.filters.compositions';

    public $verbs = [
        ICRUDVerb::VIEW,
        ICRUDVerb::SHOW,
        ICRUDVerb::CREATE,
        ICRUDVerb::EDIT,
        ICRUDVerb::DELETE,
    ];
    public $createValidations = [
        'data.title' => 'required|string|max:190',
        'name' => 'required|string|max:190',
        'type' => 'required|string|max:190',
        'zorder' => 'required|numeric',
    ];
    public $updateValidations = [
        'data.title' => 'required|string|max:190',
        'name' => 'required|string|max:190',
        'type' => 'required|string|max:190',
        'zorder' => 'required|numeric',
    ];
    public $validSortColumns = [
        'id',
        'name',
        'type',
        'author_id',
        'created_at',
        'updated_at',
    ];
    public $searchColumns = [
        'id' => 'equals:id',
        'name' => 'equals:name',
        'type' => 'euqlas:type',
    ];

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getValidRelations(): array
    {
        return [
            'author' => config('larapress.crud.user.provider'),
        ];
    }

    /**
     * Undocumented function
     *
     * @param array $args
     *
     * @return array
     */
    public function onBeforeCreate(array $args): array
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        $args['author_id'] = $user->id;

        return $args;
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function onBeforeQuery(Builder $query): Builder
    {
        /** @var ICRUDUser $user */
        $user = Auth::user();
        if (! $user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            $query->where('author_id', $user->id);
        }

        return $query;
    }

    /**
     * @param Filter $object
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
}
