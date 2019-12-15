<?php



namespace Larapress\Profiles\MetaData;

use Larapress\CRUD\Base\BasePermissionMetaData;
use Larapress\CRUD\Base\IPermissionsMetaData;
use Larapress\CRUD\Base\SingleSourceBaseMetaData;
use Larapress\CRUDRender\CRUD\BaseCRUDPermissionView;
use Larapress\CRUDRender\CRUD\ICRUDPermissionView;
use Larapress\CRUDRender\Form\BaseCRUDFormMetaData;
use Larapress\CRUDRender\Form\ICRUDFormMetaData;
use Larapress\CRUDRender\Rendering\Menu\IMenuItemMetaData;
use Larapress\CRUDRender\Rendering\Table\ITableViewMetaData;
use Larapress\CRUDRender\Rendering\Table\TableViewColumn;
use Larapress\CRUDRender\Rendering\Table\TableViewMetaData;

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
     * Permission group name
     *
     * @return string
     */
    public function getPermissionObjectName()
    {
        return config('larapress.profiles.routes.activity-logs.name');
    }

    public function title()
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

    public function key()
    {
        return 'activity-logs';
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
            ]
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
