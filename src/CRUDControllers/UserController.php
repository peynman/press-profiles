<?php

namespace Larapress\Profiles\CRUDControllers;

use Larapress\CRUD\Base\ICRUDService;
use Larapress\CRUD\Controllers\BaseCRUDController;
use Larapress\Profiles\CRUD\UserCRUDProvider;

class UserController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.users.name'),
            self::class,
            UserCRUDProvider::class
        );
    }
}
