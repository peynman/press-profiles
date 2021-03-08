<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\CRUDControllers\BaseCRUDController;
use Larapress\Profiles\CRUD\ActivityLogCRUDProvider;

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
