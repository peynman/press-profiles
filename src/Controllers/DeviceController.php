<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\CRUDController;

/**
 * Standard CRUD Controller for Device resource.
 *
 * @group Device Management
 */
class DeviceController extends CRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.devices.name'),
            self::class,
            config('larapress.profiles.routes.devices.provider'),
        );
    }
}
