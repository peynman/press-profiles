<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\BaseCRUDController;
use Larapress\Profiles\CRUD\ActivityLogCRUDProvider;

/**
 * Standard CRUD Controller for Activity Log resource.
 *
 * @group Activity Log Management
 */
class ActivityLogController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.activity-logs.name'),
            self::class,
            ActivityLogCRUDProvider::class
        );
    }
}
