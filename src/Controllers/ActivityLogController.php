<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\CRUDController;
use Larapress\Profiles\CRUD\ActivityLogCRUDProvider;

/**
 * Standard CRUD Controller for Activity Log resource.
 *
 * @group Activity Log Management
 */
class ActivityLogController extends CRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.activity_logs.name'),
            self::class,
            config('larapress.profiles.routes.activity_logs.provider'),
        );
    }
}
