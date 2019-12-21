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
use Larapress\Profiles\CRUDControllers\ActivateCodeHistoryController;
use Larapress\Profiles\Models\ActivateCode;

class ActivateCodeHistoryMetaData extends SingleSourceBaseMetaData implements
    IPermissionsMetaData,
    ICRUDPermissionView,
    ITableViewMetaData,
    IMenuItemMetaData,
    ICRUDFormMetaData
{
    use BasePermissionMetaData;
    use BaseCRUDPermissionView;
    use TableViewMetaData {
        queryUrl as protected queryAllUrl;
    }
    use BaseCRUDFormMetaData {
        viewUrl as protected viewAllUrl;
    }

    /** @var string|int $ActivateCodeID */
    public $ActivateCodeID = null;
    /** @var ActivateCode $ActivateCode */
    public $ActivateCode = null;

    protected function __construct()
    {
        $this->ActivateCodeID = ActivateCodeHistoryController::getActivateCodeIDFromRequest();
        if (! is_null($this->ActivateCodeID)) {
            $this->ActivateCode = ActivateCode::find($this->ActivateCodeID);
        }
    }

    /***
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
        return config('larapress.profiles.routes.activate-codes-history.name');
    }

    /**
     * @return string
     */
    public function getMenuTitle()
    {
        return trans('models.activate-codes-history.sidebar');
    }

    /**
     * @return string
     */
    public function singular()
    {
        return trans('models.activate-codes-history.name.singular');
    }

    /**
     * @return string
     */
    public function plural()
    {
        return trans('models.activate-codes-history.name.plural');
    }

    /**
     * @return bool
     */
    public function hasCreate()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function hasEdit()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getMenuKey()
    {
        return config('larapress.profiles.routes.activate-codes-history.name');
    }

    /**
     * @return string
     */
    public function getMenuIcon()
    {
        return 'devices';
    }

    /**
     * @return string
     */
    public function getMenuURL()
    {
        return $this->viewUrl();
    }

    /**
     * @return string
     */
    public function queryUrl()
    {
        if (! is_null($this->ActivateCodeID)) {
            return route(
                config('larapress.profiles.routes.activate-codes-history.name').'.query.specific',
                $this->ActivateCodeID
            );
        }

        return $this->queryAllUrl();
    }

    /**
     * @return string
     */
    public function viewUrl()
    {
        if (! is_null($this->ActivateCodeID)) {
            return route(
                config('larapress.profiles.routes.activate-codes-history.name').'.view.specific',
                $this->ActivateCodeID
            );
        }

        return $this->viewAllUrl();
    }

    /**
     * @return array
     */
    public function getMenuViewPermissions()
    {
        return [$this->getViewPermission()];
    }

    /**
     * @return array
     */
    public function getMenuViewRoles()
    {
        return [];
    }

    public function getTableColumns()
    {
        return [
            TableViewColumn::id(),
            TableViewColumn::column(trans('tables.column.ip'), 'ip'),
            TableViewColumn::column(trans('tables.column.username'), 'user_agent'),
            TableViewColumn::datetime(trans('tables.column.created_at'), 'created_at'),
            TableViewColumn::options(),
        ];
    }

    public function getFilterFields()
    {
        return [];
    }

    public function getControllerRouteName()
    {
        return config('larapress.profiles.routes.activate-codes-history.name');
    }

    public function queryParams()
    {
        return ['with' => []];
    }

    public function getViewControllerRouteName()
    {
        return config('larapress.profiles.routes.activate-codes-history.name');
    }

    public function getCreateFields()
    {
        return [];
    }

    public function getUpdateFields($object = null)
    {
        return $this->getCreateFields();
    }
}
