<?php

namespace Larapress\Profiles\CRUDRenderControllers;

use Larapress\CRUDRender\Base\BaseCRUDRenderController;
use Larapress\CRUDRender\Base\ICRUDRenderProvider;
use Larapress\Profiles\MetaData\EmailAddressMetaData;

class EmailAddressRenderController extends BaseCRUDRenderController
{
    public static function registerRoutes()
    {
        parent::registerRenderRoutes(
            config('larapress.profiles.routes.emails.name'),
            self::class,
            EmailAddressMetaData::class,
            ICRUDRenderProvider::class
        );
    }
}
