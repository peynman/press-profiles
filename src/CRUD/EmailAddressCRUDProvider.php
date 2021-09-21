<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Exceptions\AppException;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\EmailAddress;

class EmailAddressCRUDProvider implements ICRUDProvider
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
        'data' => 'nullable|json_object',
    ];
    public $validSortColumns = [
        'id',
        'email',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public $searchColumns = [
        'email'
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
            'email' => 'required|email|unique:email_addresses,email,'.$request->route('id'),
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
     *
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
            return in_array($object->domain_id, $user->getAffiliateDomainIds());
        }

        return true;
    }
}
