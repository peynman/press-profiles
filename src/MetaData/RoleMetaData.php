<?php

namespace Larapress\Profiles\MetaData;

use Larapress\CRUD\Base\BasePermissionMetaData;
use Larapress\CRUD\Base\IPermissionsMetaData;
use Larapress\CRUD\Base\SingleSourceBaseMetaData;
use Larapress\CRUDRender\CRUD\BaseCRUDPermissionView;
use Larapress\CRUDRender\CRUD\ICRUDPermissionView;
use Larapress\CRUDRender\Form\BaseCRUDFormMetaData;
use Larapress\CRUDRender\Form\FormField;
use Larapress\CRUDRender\Form\ICRUDFormMetaData;
use Larapress\CRUDRender\Menu\IMenuItemMetaData;
use Larapress\CRUDRender\Table\ITableViewMetaData;
use Larapress\CRUDRender\Table\TableViewColumn;
use Larapress\CRUDRender\Table\TableViewMetaData;
use Larapress\Profiles\Models\Permission;

class RoleMetaData extends SingleSourceBaseMetaData implements
    IPermissionsMetaData,
    ICRUDPermissionView,
    IMenuItemMetaData,
    ITableViewMetaData,
    ICRUDFormMetaData
{
    use BasePermissionMetaData;
    use BaseCRUDPermissionView;
    use TableViewMetaData;
    use BaseCRUDFormMetaData;

    public function getPermissionVerbs()
    {
        return [
            self::VIEW,
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ];
    }

    public function getPermissionObjectName()
    {
        return config('larapress.profiles.routes.roles.name');
    }

    public function title()
    {
        return trans('sidebar.title.roles');
    }

    public function singular()
    {
        return trans('models.role.name.singular');
    }

    public function plural()
    {
        return trans('models.role.name.plural');
    }

    public function key()
    {
        return config('larapress.profiles.routes.roles.name');
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
            TableViewColumn::column(trans('tables.column.name'), 'name'),
            TableViewColumn::column(trans('tables.column.title'), 'title'),
            TableViewColumn::datetime(trans('tables.column.created_at')),
            TableViewColumn::options(),
        ];
    }

    public function getControllerRouteName()
    {
        return config('larapress.profiles.routes.roles.name');
    }

    public function queryParams()
    {
        return [];
    }

    public function getViewControllerRouteName()
    {
        return config('larapress.profiles.routes.roles.name');
    }

    public function getCreateFields()
    {
        return [
            FormField::text('name', trans('forms.label.name')),
            FormField::text('title', trans('forms.label.title')),
            FormField::objectGroup(
                'permissions',
                trans('forms.label.permissions'),
                Permission::all(['id', 'title', 'name', 'group_name', 'group_title']),
                FormField::decorator('id', 'title', 'group_name', 'group_title'),
                []
            ),
        ];
    }

    public function getUpdateFields($object = null)
    {
        return [
            FormField::text('name', trans('forms.label.name'), null, null, 'text', true),
            FormField::text('title', trans('forms.label.title')),
            FormField::objectGroup(
                'permissions',
                trans('forms.label.permissions'),
                Permission::all(['id', 'title', 'name', 'group_name', 'group_title']),
                FormField::decorator('id', 'title', 'group_name', 'group_title'),
                []
            ),
        ];
    }
}
