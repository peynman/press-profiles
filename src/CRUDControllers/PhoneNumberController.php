<?php


namespace Larapress\Profiles\CRUDControllers;

use Larapress\CRUD\Base\ICRUDService;
use Larapress\CRUD\Controllers\BaseCRUDController;
use Larapress\Profiles\CRUD\PhoneNumberCRUDProvider;

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
