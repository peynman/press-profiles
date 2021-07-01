<?php

namespace Larapress\Profiles\Controllers;

use Larapress\Profiles\CRUD\GroupCRUDProvider;
use Larapress\CRUD\Services\CRUD\CRUDController;

/**
 * Standard CRUD Controller for Group resource.
 *
 * @group Groups Management
 */
class GroupController extends CRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.groups.name'),
            self::class,
            config('larapress.profiles.routes.groups.provider'),
        );
    }
}
