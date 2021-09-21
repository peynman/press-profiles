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

class SegmentCRUDProvider implements ICRUDProvider
{
    use CRUDProviderTrait;

    public $name_in_config = 'larapress.profiles.routes.segments.name';
    public $model_in_config = 'larapress.profiles.routes.segments.model';
    public $compositions_in_config = 'larapress.profiles.routes.segments.compositions';

    public $verbs = [
        ICRUDVerb::VIEW,
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
        'author_id',
        'created_at',
        'updated_at',
    ];
    public $searchColumns = [
        'name',
        'data',
    ];
    public $filterFields = [
        'name' => 'like:name',
        'author_id' => 'equals:author_id',
    ];

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getValidRelations(): array {
        return [
            'author' => config('larapress.crud.user.provider'),
            'members' => config('larapress.crud.user.provider'),
        ];
    }

    /**
     * @param Segment $object
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
     *
     * @return Builder
     */
    public function onBeforeQuery($query): Builder
    {
        /** @var IProfileUser $user */
        $user = Auth::user();

        if (! $user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            $query->where('author_id', $user->id);
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
    public function onBeforeCreate(array $args): array
    {
        $args['author_id'] = Auth::user()->id;
        return $args;
    }
}
