<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\CRUDController;
use Larapress\Profiles\CRUD\UserCRUDProvider;

/**
 * Standard CRUD Controller for User management
 *
 * @group User Management
 */
class UserController extends CRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.users.name'),
            self::class,
            config('larapress.crud.user.provider')
        );
    }
}
