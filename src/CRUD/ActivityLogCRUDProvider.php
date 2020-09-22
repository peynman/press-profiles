<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapress\Core\Exceptions\AppException;
use Larapress\CRUD\Services\BaseCRUDProvider;
use Larapress\CRUD\Services\ICRUDProvider;
use Larapress\CRUD\Services\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\ActivityLog;

class ActivityLogCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.activity-logs.name';
    public $verbs = [
        self::VIEW,
    ];
    public $model = ActivityLog::class;
    public $validSortColumns = [
        'id',
        'type',
        'subject',
        'created_at'
    ];
    public $validRelations = ['user', 'domain'];
    public $defaultShowRelations = ['user', 'domain'];
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
     * @param Builder $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var ICRUDUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * @param Domain $object
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
