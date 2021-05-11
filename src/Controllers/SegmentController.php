<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\BaseCRUDController;
use Larapress\Profiles\CRUD\SegmentCRUDProvider;

/**
 * Standard CRUD Controller for Segment resource.
 *
 * @group Segments Management
 */
class SegmentController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.segments.name'),
            self::class,
            SegmentCRUDProvider::class,
        );
    }
}
