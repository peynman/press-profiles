<?php

namespace Larapress\Profiles\CRUDRenderControllers;

use Larapress\CRUDRender\Base\BaseCRUDRenderController;
use Larapress\CRUDRender\Base\ICRUDRenderProvider;
use Larapress\Profiles\MetaData\DomainMetaData;

class DomainRenderController extends BaseCRUDRenderController
{
    public static function registerRoutes()
    {
        parent::registerRenderRoutes(
            config('larapress.profiles.routes.domains.name'),
            self::class,
            DomainMetaData::class,
            ICRUDRenderProvider::class // use default view provider
        );
    }
}
