<?php


namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\Base\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Filter;

class FilterCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.filters.name';
    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
    ];
    public $model = Filter::class;
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
    public $validRelations = [
        'author',
    ];
    public $defaultShowRelations = [
        'author',
    ];
    public $searchColumns = [
        'id' => 'equals:id',
        'name',
        'type',
    ];

    public function onBeforeCreate($args)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        $args['author_id'] = $user->id;

        return $args;
    }

    /**
     * @param Builder $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var ICRUDUser $user */
        $user = Auth::user();
        if (! $user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            $query->where('author_id', $user->id);
        }

        return $query;
    }

    /**
     * @param Filter $object
     *
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
}
