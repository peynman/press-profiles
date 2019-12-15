<?php


namespace Larapress\Profiles\CRUD;

use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\Profiles\Models\ActivateCode;

class ActivateCodeCRUDProvider implements ICRUDProvider
{
    use BaseCRUDProvider;

    public $model = ActivateCode::class;
    public $createValidations = [
    ];
    public $updateValidations = [
        'code' => 'required|numeric|digits:8',
        'status' => 'required|numeric|min:0|max:1',
    ];
    public $autoSyncRelations = [];
    public $validSortColumns = ['id', 'code', 'mode', 'status', 'created_at'];
    public $validRelations = ['user', 'cart', 'history'];
    public $validFilters = [];
    public $defaultShowRelations = ['user', 'history'];
    public $excludeFromUpdate = [];
    public $searchColumns = [
        'id',
        'code',
        'has:user,name',
    ];
    public $filterFields = [
        'status' => 'equals:status',
    ];
    public $filterDefaults = [
        'status' => null,
    ];
}
