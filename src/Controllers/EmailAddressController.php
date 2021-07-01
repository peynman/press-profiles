<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\CRUDController;
use Larapress\Profiles\CRUD\EmailAddressCRUDProvider;

/**
 * Standard CRUD Controller for Email Address resource.
 *
 * @group Email Address Management
 */
class EmailAddressController extends CRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.emails.name'),
            self::class,
            config('larapress.profiles.routes.emails.provider'),
        );
    }
}
