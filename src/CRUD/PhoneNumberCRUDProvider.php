<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Exceptions\AppException;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;
use Larapress\Profiles\Models\PhoneNumber;
use Larapress\Profiles\IProfileUser;

class PhoneNumberCRUDProvider implements ICRUDProvider
{
    use CRUDProviderTrait;

    public $name_in_config = 'larapress.profiles.routes.phone_numbers.name';
    public $model_in_config = 'larapress.profiles.routes.phone_numbers.model';
    public $compositions_in_config = 'larapress.profiles.routes.phone_numbers.compositions';

    public $verbs = [
        ICRUDVerb::VIEW,
        ICRUDVerb::SHOW,
        ICRUDVerb::CREATE,
        ICRUDVerb::EDIT,
        ICRUDVerb::DELETE,
        ICRUDVerb::REPORTS,
    ];
    public $createValidations = [
        'user_id' => 'required|numeric|exists:users,id',
        'number' => 'required|numeric_farsi|unique:phone_numbers,number',
        'flags' => 'numeric',
        'data' => 'nullable|json_object',
    ];
    public $validSortColumns = [
        'id',
        'number',
        'flags',
        'domain_id',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public $searchColumns = [
        'number',
    ];
    public $filterFields = [
        'created_from' => 'after:created_at',
        'created_to' => 'before:created_at',
        'updated_from' => 'after:upated_at',
        'updated_to' => 'before:updated_at',
    ];

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getValidRelations(): array
    {
        return [
            'user' => config('larapress.crud.user.provider'),
            'domain' => config('larapress.profiles.routes.domains.provider'),
        ];
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'user_id' => 'required|numeric|exists:users,id',
            'number' => 'required|numeric_farsi|unique:phone_numbers,number,'.$request->route('id'),
            'flags' => 'numeric',
            'data' => 'nullable|json_object',
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
     *
     * @return Builder
     */
    public function onBeforeQuery($query): Builder
    {
        /** @var IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            $query->whereIn('domain_id', $user->getAffiliateDomainIds());
        }

        return $query;
    }

    /**
     * @param PhoneNumber $object
     *
     * @return bool
     */
    public function onBeforeAccess($object): bool
    {
        /** @var IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            return in_array($object->domain_id, $user->getAffiliateDomainIds());
        }

        return true;
    }
}
