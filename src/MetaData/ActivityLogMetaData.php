<?php

namespace Larapress\Profiles\MetaData;

use Larapress\CRUD\Base\BasePermissionMetaData;
use Larapress\CRUD\Base\IPermissionsMetaData;
use Larapress\CRUD\Base\SingleSourceBaseMetaData;
use Larapress\CRUDRender\Base\BaseCRUDPermissionView;
use Larapress\CRUDRender\Base\ICRUDPermissionView;
use Larapress\CRUDRender\Form\BaseCRUDFormMetaData;
use Larapress\CRUDRender\Form\ICRUDFormMetaData;
use Larapress\CRUDRender\Menu\IMenuItemMetaData;
use Larapress\CRUDRender\Table\ITableViewMetaData;
use Larapress\CRUDRender\Table\TableViewColumn;
use Larapress\CRUDRender\Table\TableViewMetaData;

class ActivityLogMetaData extends SingleSourceBaseMetaData implements
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
            self::DELETE,
        ];
    }

    /**
     * Permission group name.
     *
     * @return string
     */
    public function getPermissionObjectName()
    {
        return config('larapress.profiles.routes.activity-logs.name');
    }

    public function getMenuTitle()
    {
        return trans('models.activity-logs.sidebar');
    }

    public function singular()
    {
        return trans('models.activity-logs.name.singular');
    }

    public function plural()
    {
        return trans('models.activity-logs.name.plural');
    }

    public function getMenuKey()
    {
        return 'activity-logs';
    }

    public function getMenuIcon()
    {
        return '';
    }

    public function getMenuURL()
    {
        return $this->viewUrl();
    }

    /**
     * @return array
     */
    public function getMenuViewPermissions()
    {
        return [$this->getViewPermission()];
    }

    public function getMenuViewRoles()
    {
        return [];
    }

    public function hasCreate()
    {
        return false;
    }

    public function hasDelete()
    {
        return false;
    }

    public function getTableColumns()
    {
        return [
            TableViewColumn::id(),
            TableViewColumn::column(trans('tables.column.user_id'), 'user.id', false),
            TableViewColumn::column(trans('tables.column.username'), 'user.name', false),
            TableViewColumn::column(
                trans('tables.column.domain'),
                'domain.domain',
                false,
                30,
                DomainMetaData::instance()->editUrl('::domain_id:')
            ),
            TableViewColumn::column(trans('forms.label.ip_addresses'), 'data.ip'),
            TableViewColumn::column(trans('forms.label.type'), 'type'),
            TableViewColumn::column(trans('forms.label.subject'), 'subject'),
            TableViewColumn::datetime(trans('forms.label.captured_at'), 'captured_at'),
            TableViewColumn::options(),
        ];
    }

    public function getControllerRouteName()
    {
        return config('larapress.profiles.routes.activity-logs.name');
    }

    public function queryParams()
    {
        return [
            'with' => [
                'user' => [],
                'domain' => [],
            ],
        ];
    }

    public function getViewControllerRouteName()
    {
        return config('larapress.profiles.routes.activity-logs.name');
    }

    public function getCreateFields()
    {
        return [
        ];
    }

    public function getUpdateFields($object = null)
    {
        return $this->getCreateFields();
    }
}
