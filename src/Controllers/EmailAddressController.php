<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\BaseCRUDController;
use Larapress\Profiles\CRUD\EmailAddressCRUDProvider;

/**
 * Standard CRUD Controller for Email Address resource.
 *
 * @group Email Address Management
 */
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
