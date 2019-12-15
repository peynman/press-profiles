<?php

namespace Larapress\Profiles\CRUDControllers;

use Larapress\CRUD\Base\ICRUDService;
use Larapress\CRUD\Controllers\BaseCRUDController;
use Larapress\Profiles\CRUD\RoleCRUDProvider;

class RoleController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.roles.name'),
            self::class,
            RoleCRUDProvider::class
        );
    }
}
