<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\CRUDController;
use Larapress\Profiles\CRUD\PhoneNumberCRUDProvider;

/**
 * Standard CRUD Controller for PhysicalAddress resource.
 *
 * @group Physical Address Management
 */
class PhysicalAddressController extends CRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.addresses.name'),
            self::class,
            config('larapress.profiles.routes.addresses.provider'),
        );
    }
}
