<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Support\Facades\Auth;
use Larapress\Core\Exceptions\AppException;
use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\Base\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\ActivityLog;

class ActivityLogCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.activity-logs.name';
    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
    ];
    public $model = ActivityLog::class;
    public $data_keys = [
    ];
    public $createValidations = [
    ];
    public $updateValidations = [
    ];
    public $validSortColumns = ['id',  'type', 'subject', 'captured_at'];
    public $validRelations = ['user', 'domain'];
    public $validFilters = [];
    public $defaultShowRelations = ['user', 'domain'];
    public $excludeFromUpdate = [];
    public $autoSyncRelations = [];
    public $searchColumns = [
        'equals:id',
        'equals:type',
        'subject',
    ];
    public $filterFields = [
        'type' => 'equals:type',
        'subject' => 'subject',
        'domain_id' => 'equals:domain_id',
    ];
    public $filterDefaults = [
        'type' => null,
        'subject' => null,
        'domain_id' => null,
    ];

    /**
     * @param $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var IProfileUser|ICRUDUser $user */
        $user = Auth::user();

        if ($user->hasRole(config('bet.master.role_name'))) {
            $query->whereIn('domain_id', $user->getAffiliateDomainIds());
        }

        return $query;
    }

    /**
     * @param $args
     *
     * @return array|void
     * @throws AppException
     */
    public function onBeforeCreate($args)
    {
        throw new AppException(AppException::ERR_OBJ_ACCESS_DENIED);
    }

    /**
     * @param array $args
     *
     * @return array|void
     * @throws AppException
     */
    public function onBeforeUpdate($args)
    {
        throw new AppException(AppException::ERR_OBJ_ACCESS_DENIED);
    }
}
