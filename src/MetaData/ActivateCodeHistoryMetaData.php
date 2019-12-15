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
        if (!is_null($this->ActivateCodeID)) {
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
    public function title()
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
    public function key()
    {
        return config('larapress.profiles.routes.activate-codes-history.name');
    }

    /**
     * @return string
     */
    public function icon()
    {
        return 'devices';
    }

    /**
     * @return string
     */
    public function url()
    {
        return $this->viewUrl();
    }

    /**
     * @return string
     */
    public function queryUrl()
    {
        if (!is_null($this->ActivateCodeID)) {
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
        if (!is_null($this->ActivateCodeID)) {
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
    public function viewPermissions()
    {
        return [$this->getViewPermission()];
    }
    /**
     * @return array
     */
    public function viewRoles()
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
            TableViewColumn::options()
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
