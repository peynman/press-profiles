<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\RBAC\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Form;

class FormCRUDProvider implements ICRUDProvider
{
    use CRUDProviderTrait;

    public $name_in_config = 'larapress.profiles.routes.forms.name';
    public $model_in_config = 'larapress.profiles.routes.forms.model';
    public $compositions_in_config = 'larapress.profiles.routes.forms.compositions';

    public $verbs = [
        ICRUDVerb::VIEW,
        ICRUDVerb::SHOW,
        ICRUDVerb::CREATE,
        ICRUDVerb::EDIT,
        ICRUDVerb::DELETE,
        ICRUDVerb::REPORTS,
    ];
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
    public $validSortColumns = [
        'id',
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public $searchColumns = [
        'name',
        'data',
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
            'entries' => config('larapress.profiles.routes.form_entries.provider'),
        ];
    }

    /**
     * Exclude current id in name unique request
     *
     * @param Request $request
     *
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        $this->updateValidations['name'] .= ',' . $request->route('id');
        return $this->updateValidations;
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

    /**
     * @param Form $object
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
            $query->where('author_id', $user->id);
        }

        return $query;
    }
}
