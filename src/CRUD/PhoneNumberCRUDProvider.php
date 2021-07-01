<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\RBAC\IPermissionsMetadata;
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
        ICRUDVerb::CREATE,
        ICRUDVerb::EDIT,
        ICRUDVerb::DELETE,
        ICRUDVerb::REPORTS,
    ];
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
     * @param Builder $query
     *
     * @return Builder
     */
    public function onBeforeQuery($query): Builder
    {
        /** @var IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
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
