<?php

namespace Larapress\Profiles\CRUDControllers;

use Larapress\CRUD\Controllers\BaseCRUDController;
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
