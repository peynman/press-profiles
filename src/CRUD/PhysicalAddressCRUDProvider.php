<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\RBAC\IPermissionsMetadata;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\EmailAddress;

class PhysicalAddressCRUDProvider implements ICRUDProvider
{
    use CRUDProviderTrait;

    public $name_in_config = 'larapress.profiles.routes.emails.name';
    public $model_in_config = 'larapress.profiles.routes.emails.model';
    public $compositions_in_config = 'larapress.profiles.routes.emails.compositions';

    public $verbs = [
        ICRUDVerb::VIEW,
        ICRUDVerb::CREATE,
        ICRUDVerb::EDIT,
        ICRUDVerb::DELETE,
    ];
    public $createValidations = [
        'user_id' => 'required|numeric|exists:users,id',
        'email' => 'required|email|unique:email_addresses,email',
        'flags' => 'numeric',
        'data' => 'nullable|json',
    ];
    public $updateValidations = [
        'user_id' => 'required|numeric|exists:users,id',
        'email' => 'required|email|unique:email_addresses,email',
        'flags' => 'numeric',
        'data' => 'nullable|json',
    ];
    public $validSortColumns = [
        'id',
        'email',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public $validRelations = [
        'user',
        'domain',
    ];
    public $defaultShowRelations = [
        'user',
        'domain'
    ];
    public $searchColumns = [
        'email'
    ];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function onBeforeQuery(Builder $query): Builder
    {
        /** @var IProfileUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.customer'))) {
            $query->where('user_id', $user->id);
        }
        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            $query->whereIn('domain_id', $user->getAffiliateDomainIds());
        }

        return $query;
    }

    /**
     * @param EmailAddress $object
     * @return bool
     */
    public function onBeforeAccess($object): bool
    {
        /** @var IProfileUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.customer'))) {
            return $user->id === $object->user_id;
        }
        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            return in_array($object->user_id, $user->getAffiliateDomainIds());
        }

        return true;
    }
}
