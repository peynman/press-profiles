<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\CRUDController;
use Larapress\Profiles\CRUD\SegmentCRUDProvider;

/**
 * Standard CRUD Controller for Segment resource.
 *
 * @group Segments Management
 */
class SegmentController extends CRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.segments.name'),
            self::class,
            config('larapress.profiles.routes.segments.provider'),
        );
    }
}
