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
use Larapress\Profiles\Models\Domain;

class FilterMetaData extends SingleSourceBaseMetaData implements
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

    /***
     * @return string
     */
    public function getPermissionObjectName()
    {
        return config('larapress.profiles.routes.filters.name');
    }

    public function title()
    {
        return trans('sidebar.title.filters');
    }

    public function singular()
    {
        return trans('models.filter.name.singular');
    }

    public function plural()
    {
        return trans('models.filter.name.plural');
    }

    public function key()
    {
        return config('larapress.profiles.routes.filters.name');
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
                trans('tables.column.domain'),
                'domain.domain',
                false,
                30,
                DomainMetaData::instance()->editUrl('::domain_id:')
            ),
            TableViewColumn::column(trans('tables.column.title'), 'title'),
            TableViewColumn::column(trans('tables.column.name'), 'name'),
            TableViewColumn::column(trans('tables.column.type'), 'type'),
            TableViewColumn::options(),
        ];
    }

    public function getControllerRouteName()
    {
        return config('larapress.profiles.routes.filters.name');
    }

    public function queryParams()
    {
        return [
            'with' => [
                'domain' => [],
            ],
        ];
    }

    public function getViewControllerRouteName()
    {
        return config('larapress.profiles.routes.filters.name');
    }

    public function getCreateFields()
    {
        return [
            FormField::text('title', trans('forms.label.title')),
            FormField::text('name', trans('forms.label.name')),
            FormField::text('type', trans('forms.label.type')),
            FormField::common('select', 'sub_domain_id', trans('forms.label.domain'), [
                'objects' => Domain::select(['id', 'domain'])->get(),
                'decorator' => FormField::decorator('id', 'domain'),
                'multiple' => true,
                'max-options' => 1,
            ]),
            FormField::common('json', 'data', trans('forms.label.data')),
        ];
    }

    public function getUpdateFields($object = null)
    {
        return [
            FormField::text('title', trans('forms.label.title')),
            FormField::text('name', trans('forms.label.name')),
            FormField::text('type', trans('forms.label.type')),
            FormField::common('select', 'sub_domain_id', trans('forms.label.domain'), [
                'objects' => Domain::select(['id', 'domain'])->get(),
                'decorator' => FormField::decorator('id', 'domain'),
                'multiple' => true,
                'max-options' => 1,
            ]),
            FormField::common('json', 'data', trans('forms.label.data')),
        ];
    }
}
