<?php

namespace Larapress\Profiles\CRUDRenderControllers;

use Larapress\CRUDRender\Base\BaseCRUDRenderController;
use Larapress\CRUDRender\Base\ICRUDRenderProvider;
use Larapress\Profiles\MetaData\UserMetaData;

class UserRenderController extends BaseCRUDRenderController
{
    public static function registerRoutes()
    {
        parent::registerRenderRoutes(
            config('larapress.profiles.routes.users.name'),
            self::class,
            UserMetaData::class,
            ICRUDRenderProvider::class
        );
    }
}
