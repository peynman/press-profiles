<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\EmailAddress;

class EmailAddressCRUDProvider implements ICRUDProvider
{
    use BaseCRUDProvider;

    public $model = EmailAddress::class;
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
    public $autoSyncRelations = ['user'];
    public $validSortColumns = ['id', 'email', 'created_at'];
    public $validRelations = ['user', 'domain'];
    public $validFilters = [];
    public $defaultShowRelations = ['user', 'domain'];
    public $excludeFromUpdate = [];
    public $searchColumns = ['email'];
    public $filterDefaults = [];
    public $filterFields = [];

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var ICRUDUser|IProfileUser $user */
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
    public function onBeforeAccess($object)
    {
        /** @var ICRUDUser|IProfileUser $user */
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
