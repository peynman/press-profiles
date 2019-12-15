<?php

namespace Larapress\Profiles\MetaData;

use Larapress\CRUD\Base\BasePermissionMetaData;
use Larapress\CRUD\Base\IPermissionsMetaData;
use Larapress\CRUD\Base\SingleSourceBaseMetaData;
use Larapress\CRUDRender\CRUD\BaseCRUDPermissionView;
use Larapress\CRUDRender\Form\BaseCRUDFormMetaData;
use Larapress\CRUDRender\Form\FormField;
use Larapress\CRUDRender\Form\ICRUDFormMetaData;
use Larapress\CRUDRender\Rendering\Menu\IMenuItemMetaData;
use Larapress\CRUDRender\Rendering\Table\ITableViewMetaData;
use Larapress\CRUDRender\Rendering\Table\TableViewColumn;
use Larapress\CRUDRender\Rendering\Table\TableViewMetaData;

class PhoneNumberMetaData extends SingleSourceBaseMetaData implements
    IPermissionsMetaData,
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
     * Permission group name.
     *
     * @return string
     */
    public function getPermissionObjectName()
    {
        return config('larapress.profiles.routes.phone-numbers.name');
    }

    public function title()
    {
        return trans('sidebar.title.numbers');
    }

    public function singular()
    {
        return trans('models.phone-number.name.singular');
    }

    public function plural()
    {
        return trans('models.phone-number.name.plural');
    }

    public function key()
    {
        return config('larapress.profiles.routes.phone-numbers.name');
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
            TableViewColumn::column(
                trans('tables.column.username'),
                'user.name',
                false,
                30,
                UserMetaData::instance()->editUrl('::user_id:')
            ),
            TableViewColumn::column(
                trans('tables.column.domain'),
                'sub_domain.domain',
                false,
                30,
                DomainMetaData::instance()->editUrl('::sub_domain_id:')
            ),
            TableViewColumn::column(trans('tables.column.number'), 'number'),
            TableViewColumn::column(trans('tables.column.type'), 'type'),
            TableViewColumn::column(trans('tables.column.description'), 'desc'),
            TableViewColumn::options(),
        ];
    }

    public function getControllerRouteName()
    {
        return config('larapress.profiles.routes.phone-numbers.name');
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
        return config('larapress.profiles.routes.phone-numbers.name');
    }

    public function getCreateFields()
    {
        return [];
    }

    public function hasCreate()
    {
        return false;
    }

    public function getUpdateFields($object = null)
    {
        return [
            FormField::common('hidden', 'user_id', 'id'),
            FormField::text('number', trans('forms.label.number')),
            FormField::text('type', trans('forms.label.type')),
            FormField::text('desc', trans('forms.label.description')),
            FormField::text('flags', trans('forms.label.options')),
        ];
    }
}
