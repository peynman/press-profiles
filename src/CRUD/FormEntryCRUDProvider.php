<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\Base\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\FormEntry;
use Larapress\Profiles\Services\FormEntryUpdateReport;
use Larapress\Reports\Services\IReportsService;

class FormEntryCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.form-entries.name';
    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
        self::REPORTS,
    ];
    public $model = FormEntry::class;
    public $createValidations = [
    ];
    public $updateValidations = [
    ];
    public $autoSyncRelations = [
    ];
    public $validSortColumns = [
        'id',
        'user_id',
        'domain_id',
        'form_id',
    ];
    public $validFilters = [
    ];
    public $validRelations = [
        'user',
        'form',
        'domain'
    ];
    public $defaultShowRelations = [
        'user',
        'form',
        'domain'
    ];
    public $excludeIfNull = [
    ];
    public $searchColumns = [
    ];
    public $filterDefaults = [
    ];
    public $filterFields = [
        'type' => 'equals:type',
        'domain' => 'equals:domain_id',
        'user_id' => 'equals:user_id',
    ];

    /**
     *
     */
    public function getReportSources()
    {
        /** @var IReportsService */
        $service = app(IReportsService::class);
        return [
            new FormEntryUpdateReport($service),
        ];
    }

    /**
     * @param FormEntry $object
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            return in_array($object->domain_id, $user->getAffiliateDomainIds());
        }

        return true;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            $query->whereIn('domain_id', $user->getAffiliateDomainIds());
        }

        return $query;
    }
}
