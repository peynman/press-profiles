<?php

namespace Larapress\Profiles\CRUDControllers;

use Larapress\CRUD\CRUDControllers\BaseCRUDController;
use Larapress\Profiles\CRUD\FormEntryCRUDProvider;

class FormEntryController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.form-entries.name'),
            self::class,
            FormEntryCRUDProvider::class
        );
    }
}