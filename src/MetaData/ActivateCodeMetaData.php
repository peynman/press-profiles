<?php

namespace Larapress\Profiles\MetaData;

use Larapress\CRUD\Base\BasePermissionMetaData;
use Larapress\CRUD\Base\ICRUDFilterStorage;
use Larapress\CRUD\Base\IPermissionsMetaData;
use Larapress\CRUD\Base\SingleSourceBaseMetaData;
use Larapress\CRUDRender\CRUD\BaseCRUDPermissionView;
use Larapress\CRUDRender\CRUD\ICRUDPermissionView;
use Larapress\CRUDRender\Form\BaseCRUDFormMetaData;
use Larapress\CRUDRender\Form\FormField;
use Larapress\CRUDRender\Form\ICRUDFormMetaData;
use Larapress\CRUDRender\Rendering\Menu\IMenuItemMetaData;
use Larapress\CRUDRender\Rendering\Table\ITableViewMetaData;
use Larapress\CRUDRender\Rendering\Table\TableViewColumn;
use Larapress\CRUDRender\Rendering\Table\TableViewMetaData;
use Larapress\Profiles\CRUD\ActivateCodeCRUDProvider;
use Larapress\Profiles\Flags\ActivateCodeMode;
use Larapress\Profiles\Flags\ActivateCodeStatus;

class ActivateCodeMetaData extends SingleSourceBaseMetaData implements
    IPermissionsMetaData,
    ICRUDPermissionView,
    ITableViewMetaData,
    IMenuItemMetaData,
    ICRUDFormMetaData
{
    use BasePermissionMetaData;
    use BaseCRUDPermissionView;
    use TableViewMetaData;
    use BaseCRUDFormMetaData;

    /***
     * get permissions required for each CRUD operation
     *
     * @return array
     */
    public function getPermissionVerbs()
    {
        return [
            self::VIEW,
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ];
    }

    /**
     * @return string
     */
    public function getPermissionObjectName()
    {
        return config('larapress.profiles.routes.activate-codes.name');
    }

    public function title()
    {
        return trans('models.activate-codes.sidebar');
    }

    public function singular()
    {
        return trans('models.activate-codes.name.singular');
    }

    public function plural()
    {
        return trans('models.activate-codes.name.plural');
    }

    public function hasCreate()
    {
        return false;
    }

    public function key()
    {
        return config('larapress.profiles.routes.activate-codes.name');
    }

    public function icon()
    {
        return '';
    }

    public function url()
    {
        return $this->viewUrl();
    }

    /**
     * @return array
     */
    public function viewPermissions()
    {
        return [$this->getViewPermission()];
    }

    public function viewRoles()
    {
        return [];
    }

    public function getTableColumns()
    {
        return [
            TableViewColumn::id(),
            TableViewColumn::column(trans('tables.column.code'), 'code'),
            TableViewColumn::column(trans('tables.column.user_id'), 'user.id'),
            TableViewColumn::column(trans('tables.column.username'), 'user.name'),
            TableViewColumn::filter(trans('tables.column.mode'), 'mode', 'JSBridge.getActivateCodeMode(data)'),
            TableViewColumn::filter(trans('tables.column.status'), 'status', 'JSBridge.getActivateCodeStatus(data)'),
            TableViewColumn::filter(trans('tables.column.use_count'), 'history', 'JSBridge.getObjectCount(data)'),
            TableViewColumn::datetime(trans('tables.column.created_at'), 'created_at'),
            TableViewColumn::options(),
        ];
    }

    public function actions()
    {
        return [
            [
                'icon' => ActivateCodeHistoryMetaData::instance()->icon(),
                'title' => ActivateCodeHistoryMetaData::instance()->plural(),
                'link' => function (...$params) {
                    ActivateCodeHistoryMetaData::instance()->ActivateCodeID = $params;

                    return ActivateCodeHistoryMetaData::instance()->viewUrl();
                },
                'metadata' => ActivateCodeHistoryMetaData::instance(),
            ],
        ];
    }

    /**
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getFilterFields()
    {
        /** @var ICRUDFilterStorage $filter */
        $filter = app()->make(ICRUDFilterStorage::class);
        $values = $filter->getFilterValues(session()->getId(), new ActivateCodeCRUDProvider());

        return [
            FormField::common('select', 'status', trans('forms.label.status'), [
                'objects' => ActivateCodeStatus::toArray(),
                'decorator' => FormField::decorator('id', 'title'),
                'value' => $values['status'],
                'multiple' => true,
            ]),
            FormField::applyFilter(),
            FormField::removeFilter(),
        ];
    }

    public function getControllerRouteName()
    {
        return config('larapress.profiles.routes.activate-codes.name');
    }

    public function queryParams()
    {
        return ['with' => ['user' => [], 'history' => []]];
    }

    public function getViewControllerRouteName()
    {
        return config('larapress.profiles.routes.activate-codes.name');
    }

    public function getCreateFields()
    {
        return [
            FormField::text('code', trans('forms.label.code')),
            FormField::common('select', 'status', trans('forms.label.status'), [
                'objects' => ActivateCodeStatus::toArray(),
                'decorator' => FormField::decorator('id', 'title'),
                'multiple' => true,
                'max-options' => 1,
            ]),
            FormField::common('select', 'mode', trans('forms.label.mode'), [
                'objects' => ActivateCodeMode::toArray(),
                'decorator' => FormField::decorator('id', 'title'),
                'multiple' => true,
                'max-options' => 1,
            ]),
        ];
    }

    public function getUpdateFields($object = null)
    {
        return $this->getCreateFields();
    }
}
