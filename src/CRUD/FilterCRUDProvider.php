<?php


namespace Larapress\Profiles\CRUD;

use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\Models\Filter;

class FilterCRUDProvider implements ICRUDProvider
{
    use BaseCRUDProvider;

    public $model = Filter::class;
    public $createValidations = [
        'name' => 'required|string|max:190',
        'title' => 'required|string|max:190',
        'type' => 'required|string|max:190',
        'data' => 'nullable|json',
        'domain_id' => 'nullable|exists:domains,id',
    ];
    public $updateValidations = [
        'name' => 'required|string|max:190',
        'title' => 'required|string|max:190',
        'type' => 'required|string|max:190',
        'data' => 'nullable|json',
        'domain_id' => 'nullable|exists:domains,id',
    ];
    public $autoSyncRelations = [];
    public $validSortColumns = ['id', 'name', 'type'];
    public $validRelations = ['domain'];
    public $validFilters = [];
    public $defaultShowRelations = ['domain'];
    public $excludeFromUpdate = [];
    public $searchColumns = ['name', 'type'];
    public $filterFields = [];
    public $filterDefaults = [];

    public function onBeforeCreate($args)
    {
        $args['flags'] = 0;

        return $args;
    }

    public function onBeforeUpdate($args)
    {
        return $args;
    }

    /**
     * @param Filter $object
     * @param array  $input_data
     *
     * @return array|void
     */
    public function onAfterCreate($object, $input_data)
    {
        Filter::resetSelectorObjectsCache();
    }
    public function onAfterUpdate($object, $input_data)
    {
        Filter::resetSelectorObjectsCache();
    }
}
