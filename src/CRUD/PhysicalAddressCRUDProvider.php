<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Exceptions\AppException;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\RBAC\IPermissionsMetadata;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\EmailAddress;

class PhysicalAddressCRUDProvider implements ICRUDProvider
{
    use CRUDProviderTrait;

    public $name_in_config = 'larapress.profiles.routes.addresses.name';
    public $model_in_config = 'larapress.profiles.routes.addresses.model';
    public $compositions_in_config = 'larapress.profiles.routes.addresses.compositions';

    public $verbs = [
        ICRUDVerb::VIEW,
        ICRUDVerb::SHOW,
        ICRUDVerb::CREATE,
        ICRUDVerb::EDIT,
        ICRUDVerb::DELETE,
    ];
    public $createValidations = [
        'user_id' => 'required|numeric|exists:users,id',
        'address' => 'required',
        'country_code' => 'required|numeric',
        'city_code' => 'nullable|numeric',
        'province_code' => 'nullable|numeric',
        'postal_code' => 'nullable|numeric',
        'data' => 'nullable|json_object',
        'flags' => 'nullable|numeric',
        'data' => 'nullable|json_object',
    ];
    public $updateValidations = [
        'user_id' => 'required|numeric|exists:users,id',
        'address' => 'required',
        'country_code' => 'required|numeric',
        'city_code' => 'nullable|numeric',
        'province_code' => 'nullable|numeric',
        'postal_code' => 'nullable|numeric',
        'data' => 'nullable|json_object',
        'flags' => 'nullable|numeric',
        'data' => 'nullable|json_object',
    ];
    public $validSortColumns = [
        'id',
        'country_code',
        'province_code',
        'city_code',
        'postal_code',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public $defaultShowRelations = [
        'user',
    ];
    public $searchColumns = [
        'address',
        'postal_code',
    ];

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getValidRelations(): array {
        return [
            'user' => config('larapress.crud.user.provider'),
        ];
    }

    /**
     * Undocumented function
     *
     * @param array $args
     * @return array
     */
    public function onBeforeCreate(array $args): array
    {
        $class = config('larapress.crud.user.model');
        /** @var IProfileUser */
        $targetUser = call_user_func([$class, 'find'], $args['user_id']);

        /** @var IProfileUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.customer'))) {
            if ($args['user_id'] !== $user->id) {
                throw new AppException(AppException::ERR_ACCESS_DENIED);
            }
        }
        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            if (is_null($targetUser) || !in_array($targetUser->getMembershipDomainId(), $user->getAffiliateDomainIds())) {
                throw new AppException(AppException::ERR_ACCESS_DENIED);
            }
        }

        $args['domain_id'] = $targetUser->getMembershipDomainId();

        return $args;
    }


    /**
     * Undocumented function
     *
     * @param array $args
     * @return array
     */
    public function onBeforeUpdate($args): array
    {
        return $this->onBeforeCreate($args);
    }

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
