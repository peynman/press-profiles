<?php

namespace Larapress\Profiles\CRUDRenderControllers;

use Larapress\CRUDRender\Base\BaseCRUDRenderController;
use Larapress\CRUDRender\Base\ICRUDRenderProvider;
use Larapress\Profiles\MetaData\ActivityLogMetaData;

class ActivityLogRenderController extends BaseCRUDRenderController
{
    public static function registerRoutes()
    {
        parent::registerRenderRoutes(
            config('larapress.profiles.routes.activity-logs.name'),
            self::class,
            ActivityLogMetaData::class,
            ICRUDRenderProvider::class // use default view provider
        );
    }
}
