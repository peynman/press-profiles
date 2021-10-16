<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;
use Larapress\Profiles\Models\Group;
use Larapress\Profiles\IProfileUser;

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
        'name' => 'required|string|unique:groups,name',
        'data.title' => 'required|string',
        'admin_user_ids.*' => 'nullable|exists:users,id',
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
     * Exclude current id in name unique request
     *
     * @param Request $request
     *
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'name' => 'required|string|unique:groups,name,'.$request->route('id'),
            'data.title' => 'required|string',
            'admin_user_ids.*' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getValidRelations(): array
    {
        return [
            'members' => config('larapress.profiles.routes.users.provider'),
            'admins' => config('larapress.profiles.routes.users.provider'),
        ];
    }

    /**
     * @param array $args
     *
     * @return array
     */
    public function onBeforeCreate($args): array
    {
        /** @var IProfileUser $user */
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
        /** @var IProfileUser */
        $user = Auth::user();

        if (! $user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            $query->orWhere('author_id', $user->id);
            $query->orWhereHas('admins', function ($q) use ($user) {
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
        /** @var IProfileUser */
        $user = Auth::user();

        if (! $user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            return in_array($object->id, $user->getAdministrateGroupIds());
        }

        return true;
    }

    /**
     * Undocumented function
     *
     * @param Group $object
     * @param array $input_data
     *
     * @return void
     */
    public function onAfterCreate($object, array $input_data): void
    {
        $object->admins()->sync($input_data['admin_user_ids']);
    }

    /**
     * Undocumented function
     *
     * @param Group $object
     * @param array $input_data
     *
     * @return void
     */
    public function onAfterUpdate($object, array $input_data): void
    {
        $object->admins()->sync($input_data['admin_user_ids']);
    }
}
