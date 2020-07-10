<?php

namespace Larapress\Profiles\CRUDControllers;

use Larapress\CRUD\CRUDControllers\BaseCRUDController;
use Larapress\Profiles\CRUD\UserCRUDProvider;

class UserController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.users.name'),
            self::class,
            config('larapress.crud.user.crud-provider')
        );
    }
}
