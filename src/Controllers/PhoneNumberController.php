<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\CRUDController;
use Larapress\Profiles\CRUD\PhoneNumberCRUDProvider;

/**
 * Standard CRUD Controller for PhoneNumber resource.
 *
 * @group Phone Number Management
 */
class PhoneNumberController extends CRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.phone_numbers.name'),
            self::class,
            config('larapress.profiles.routes.phone_numbers.provider'),
        );
    }
}
