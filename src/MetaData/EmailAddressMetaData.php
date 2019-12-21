<?php

namespace Larapress\Profiles\MetaData;

use Larapress\CRUD\Base\BasePermissionMetaData;
use Larapress\CRUD\Base\IPermissionsMetaData;
use Larapress\CRUD\Base\SingleSourceBaseMetaData;
use Larapress\CRUDRender\Base\BaseCRUDPermissionView;
use Larapress\CRUDRender\Base\ICRUDPermissionView;
use Larapress\CRUDRender\Form\BaseCRUDFormMetaData;
use Larapress\CRUDRender\Form\FormField;
use Larapress\CRUDRender\Form\ICRUDFormMetaData;
use Larapress\CRUDRender\Menu\IMenuItemMetaData;
use Larapress\CRUDRender\Table\ITableViewMetaData;
use Larapress\CRUDRender\Table\TableViewColumn;
use Larapress\CRUDRender\Table\TableViewMetaData;

class EmailAddressMetaData extends SingleSourceBaseMetaData implements
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
        return config('larapress.profiles.routes.emails.name');
    }

    public function getMenuTitle()
    {
        return trans('sidebar.title.email-addresses');
    }

    public function singular()
    {
        return trans('models.email-addresses.name.singular');
    }

    public function plural()
    {
        return trans('models.email-addresses.name.plural');
    }

    public function getMenuKey()
    {
        return config('larapress.profiles.routes.emails.name');
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

    public function getTableColumns()
    {
        return [
            TableViewColumn::id(),
            TableViewColumn::column(
                trans('tables.column.username'),
                'user.name',
                false,
                30,
                UserMetaData::instance()->editUrl('::user_id:')
            ),
            TableViewColumn::column(
                trans('tables.column.domain'),
                'domain.domain',
                false,
                30,
                DomainMetaData::instance()->editUrl('::domain_id:')
            ),
            TableViewColumn::column(trans('tables.column.email'), 'email'),
            TableViewColumn::column(trans('tables.column.type'), 'type'),
            TableViewColumn::column(trans('tables.column.description'), 'desc'),
            TableViewColumn::options(),
        ];
    }

    public function getControllerRouteName()
    {
        return config('larapress.profiles.routes.emails.name');
    }

    public function queryParams()
    {
        return [
            'with' => [
                'user' => [],
                'sub_domain' => [],
            ],
        ];
    }

    public function getViewControllerRouteName()
    {
        return config('larapress.profiles.routes.emails.name');
    }

    public function hasCreate()
    {
        return false;
    }

    public function getUpdateFields($object = null)
    {
        return [
            FormField::common('hidden', 'user_id', ''),
            FormField::text('email', trans('forms.label.email')),
            FormField::text('type', trans('forms.label.type')),
            FormField::text('desc', trans('forms.label.description')),
            FormField::text('flags', trans('forms.label.options')),
        ];
    }

    public function getCreateFields()
    {
        return [];
    }
}
