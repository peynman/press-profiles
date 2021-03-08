<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\CRUDControllers\BaseCRUDController;
use Larapress\Profiles\CRUD\EmailAddressCRUDProvider;

class EmailAddressController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.emails.name'),
            self::class,
            EmailAddressCRUDProvider::class
        );
    }
}