<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\Services\BaseCRUDProvider;
use Larapress\CRUD\Services\ICRUDProvider;
use Larapress\CRUD\Services\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\Models\PhoneNumber;
use Larapress\Profiles\IProfileUser;

class PhoneNumberCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.phone-numbers.name';
    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
        self::REPORTS,
    ];
    public $model = PhoneNumber::class;
    public $createValidations = [
        'user_id' => 'required|numeric|exists:users,id',
        'number' => 'required|numeric_farsi|unique:phone_numbers,number',
        'flags' => 'numeric',
        'data' => 'nullable|json',
    ];
    public $updateValidations = [
        'user_id' => 'required|numeric|exists:users,id',
        'number' => 'required|numeric_farsi|unique:phone_numbers,number',
        'flags' => 'numeric',
        'data' => 'nullable|json',
    ];
    public $autoSyncRelations = [
    ];
    public $validSortColumns = [
        'id',
        'number',
        'created_at',
        'updated_at',
        'flags',
        'domain_id',
        'user_id',
    ];
    public $validFilters = [];
    public $defaultShowRelations = [
        'user',
        'domain'
    ];
    public $excludeIfNull = [];
    public $searchColumns = [
        'number',
    ];
    public $filterDefaults = [];
    public $filterFields = [
        'created_from' => 'after:created_at',
        'created_to' => 'before:created_at',
    ];

    public function getValidRelations()
    {
        return [
            'user' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.users.name').'.view');
            },
            'domain' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.domains.name').'.view');
            },
            'user.phones' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.phone-numbers.name').'.view');
            },
            'user.form_support_user_profile' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.form-entries.name').'.view');
            },
            'user.form_profile_default' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.form-entries.name').'.view');
            },
            'user.form_profile_support' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.form-entries.name').'.view');
            },
            'user.form_support_registration_entry' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.form-entries.name').'.view');
            },
            'user.wallet_balance'  => function ($user) {
                return $user->hasPermission(config('larapress.ecommerce.routes.wallet_transactions.name').'.view');
            },
        ];
    }

    /**
     * @param Builder $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            $query->orWhereIn('domain_id', $user->getAffiliateDomainIds());
            $query->orWhereHas('user.form_entries', function ($q) use ($user) {
                $q->where('tags', 'support-group-'.$user->id);
            });
        }

        return $query;
    }

    /**
     * @param PhoneNumber $object
     *
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            return in_array($object->domain_id, $user->getAffiliateDomainIds());
        }

        return true;
    }
}
