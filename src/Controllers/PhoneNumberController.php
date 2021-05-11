<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\BaseCRUDController;
use Larapress\Profiles\CRUD\PhoneNumberCRUDProvider;

/**
 * Standard CRUD Controller for PhoneNumber resource.
 *
 * @group Phone Number Management
 */
class PhoneNumberController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.phone-numbers.name'),
            self::class,
            PhoneNumberCRUDProvider::class
        );
    }
}
