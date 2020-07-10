<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\Base\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Form;

class FormCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.forms.name';
    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
        self::REPORTS,
    ];
    public $model = Form::class;
    public $createValidations = [
        'name' => 'required|unique:forms,name',
        'data.title' => 'required',
        'flags' => 'nullable|numeric',
    ];
    public $updateValidations = [
        'name' => 'required|unique:forms,name',
        'data.title' => 'required|string',
        'flags' => 'nullable|numeric',
    ];
    public $autoSyncRelations = [
        'author' => 'auth::user',
    ];
    public $validSortColumns = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];
    public $validRelations = [
        'author',
        'entries',
    ];
    public $validFilters = [

    ];
    public $defaultShowRelations = [

    ];
    public $excludeIfNull = [
    ];
    public $searchColumns = [
        'name',
        'data',
    ];
    public $filterDefaults = [
    ];
    public $filterFields = [
    ];

    /**
     * Exclude current id in name unique request
     *
     * @param Request $request
     * @return void
     */
    public function getUpdateRules(Request $request) {
        $this->updateValidations['name'] .= ',' . $request->route('id');
        return $this->updateValidations;
    }


    /**
     * Undocumented function
     *
     * @param [type] $args
     * @return void
     */
    public function onBeforeCreate( $args )
    {
        $args['author_id'] = Auth::user()->id;
        return $args;
    }

    /**
     * @param Form $object
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
            $query->where('author_id', $user->id);
        }

        return $query;
    }

}
