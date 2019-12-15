<?php


namespace Larapress\Profiles\CRUD;

use Illuminate\Support\Facades\Auth;
use Larapress\Core\Extend\Helpers;
use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\Models\PhoneNumber;

class PhoneNumberCRUDProvider implements ICRUDProvider
{
    use BaseCRUDProvider;

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
    public $autoSyncRelations = ['user'];
    public $validSortColumns = ['id', 'number', 'created_at'];
    public $validRelations = ['user', 'domain'];
    public $validFilters = [];
    public $defaultShowRelations = ['user', 'domain'];
    public $excludeFromUpdate = [];
    public $searchColumns = ['number'];
    public $filterDefaults = [];
    public $filterFields = [];

    /**
     * @param array $args
     * @return array|mixed
     */
    public function onBeforeCreate($args)
    {
        $args = Helpers::getNormalizedNumbers($args, ['number']);
        return $args;
    }

    /**
     * @param array $args
     * @return array|mixed
     */
    public function onBeforeUpdate($args)
    {
        return $this->onBeforeCreate($args);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var ICRUDUser|\Larapress\Profiles\IProfileUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            $query->whereIn('domain_id', $user->getAffiliateDomainIds());
        }

        return $query;
    }

    /**
     * @param PhoneNumber $object
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var ICRUDUser|\Larapress\Profiles\IProfileUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            return in_array($object->domain_id, $user->getAffiliateDomainIds());
        }

        return true;
    }
}
