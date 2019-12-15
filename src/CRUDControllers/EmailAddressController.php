<?php


namespace Larapress\Profiles\CRUDControllers;

use Larapress\CRUD\Base\ICRUDService;
use Larapress\CRUD\Controllers\BaseCRUDController;
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
