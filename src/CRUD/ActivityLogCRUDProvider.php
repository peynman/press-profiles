<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;
use Larapress\Profiles\IProfileUser;

class ActivityLogCRUDProvider implements ICRUDProvider
{
    use CRUDProviderTrait;

    public $name_in_config = 'larapress.profiles.routes.activity_logs.name';
    public $model_in_config = 'larapress.profiles.routes.activity_logs.model';
    public $compositions_in_config = 'larapress.profiles.routes.activity_logs.compositions';

    public $verbs = [
        ICRUDVerb::VIEW,
        ICRUDVerb::SHOW,
    ];
    public $validSortColumns = [
        'id',
        'type',
        'subject',
        'created_at',
    ];
    public $searchColumns = [
        'subject',
        'description',
    ];
    public $filterFields = [
        'type' => 'equals:type',
        'subject' => 'equals:subject',
        'user_id' => 'equals:user_id',
        'domain_id' => 'equals:domain_id',
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
    public function onBeforeQuery(Builder $query): Builder
    {
        /** @var IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            $query->orWhere('user_id', $user->id);
            $query->onWhereIn('domain_id', $user->getAffiliateDomainIds());
        }

        return $query;
    }

    /**
     * @param Domain $object
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
