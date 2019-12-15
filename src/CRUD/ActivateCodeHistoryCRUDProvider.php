<?php


namespace Larapress\Profiles\CRUD;

use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\Profiles\CRUDControllers\ActivateCodeHistoryController;
use Larapress\Profiles\Models\ActivateCodeHistory;

class ActivateCodeHistoryCRUDProvider implements ICRUDProvider
{
    use BaseCRUDProvider;

    public $model = ActivateCodeHistory::class;
    public $createValidations = [
    ];
    public $updateValidations = [
    ];
    public $autoSyncRelations = [];
    public $validSortColumns = ['id', 'ip', 'user-agent', 'created_at'];
    public $validRelations = [];
    public $validFilters = [];
    public $defaultShowRelations = [];
    public $excludeFromUpdate = [];
    public $searchColumns = ['id', 'ip'];
    public $filterFields = [
    ];
    public $filterDefaults = [
    ];

    public function onBeforeQuery($query)
    {
        $specific = ActivateCodeHistoryController::getActivateCodeIDFromRequest();
        if (!is_null($specific)) {
            $query->where('activate_code_id', $specific);
        }
        return $query;
    }
}
