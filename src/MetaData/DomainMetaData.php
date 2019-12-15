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
use Larapress\Profiles\Flags\DomainFlags;

class DomainMetaData extends SingleSourceBaseMetaData implements
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
        return config('larapress.profiles.routes.domains.name');
    }

    public function title()
    {
        return trans('models.sub-domains.sidebar');
    }

    public function singular()
    {
        return trans('models.sub-domains.name.singular');
    }

    public function plural()
    {
        return trans('models.sub-domains.name.plural');
    }

    public function key()
    {
        return config('larapress.profiles.routes.domains.name');
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
                trans('tables.column.author'),
                'author.name',
                false,
                30,
                UserMetaData::instance()->editUrl('::author_id:')
            ),
            TableViewColumn::column(trans('tables.column.name'), 'name'),
            TableViewColumn::column(trans('tables.column.title'), 'title'),
            TableViewColumn::column(trans('tables.column.domain'), 'domain'),
            TableViewColumn::column(trans('tables.column.nameservers'), 'nameservers'),
            TableViewColumn::datetime(trans('tables.column.created_at'), 'created_at'),
            TableViewColumn::options(),
        ];
    }

    public function getControllerRouteName()
    {
        return config('larapress.profiles.routes.domains.name');
    }

    public function queryParams()
    {
        return [
            'with' => [
                'author' => [],
            ],
        ];
    }

    public function getViewControllerRouteName()
    {
        return config('larapress.profiles.routes.domains.name');
    }

    public function getCreateFields()
    {
        return [
            FormField::text('name', trans('forms.label.name')),
            FormField::text('title', trans('forms.label.title')),
            FormField::text('domain', trans('forms.label.domain')),
            FormField::text('nameservers', trans('forms.label.nameservers')),
            FormField::text('ips', trans('forms.label.ip_addresses')),
            FormField::common('switch', 'flags', trans('forms.label.options'), [
                'objects' => DomainFlags::toArray(),
            ]),
            FormField::common('json', 'data', trans('forms.label.data')),
        ];
    }

    public function getUpdateFields($object = null)
    {
        return [
            FormField::text('name', trans('forms.label.name'), null, null, 'text', true),
            FormField::text('title', trans('forms.label.title')),
            FormField::text('nameservers', trans('forms.label.nameservers')),
            FormField::text('domain', trans('forms.label.domain')),
            FormField::text('ips', trans('forms.label.ip_addresses')),
            FormField::common('switch', 'flags', trans('forms.label.options'), [
                'objects' => DomainFlags::toArray(),
            ]),
            FormField::common('json', 'data', trans('forms.label.data')),
        ];
    }
}
