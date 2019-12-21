<?php

namespace Larapress\Profiles\CRUDRenderControllers;

use Larapress\CRUDRender\Base\BaseCRUDRenderController;
use Larapress\CRUDRender\Base\ICRUDRenderProvider;
use Larapress\Profiles\MetaData\SettingsMetaData;

class SettingsRenderController extends BaseCRUDRenderController
{
    public static function registerRoutes()
    {
        $name = config('larapress.profiles.routes.settings.name');
        parent::registerRenderRoutes(
            $name,
            self::class,
            SettingsMetaData::class,
            ICRUDRenderProvider::class
        );
    }
}
