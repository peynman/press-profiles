<?php

namespace App\Models\Flags;

use Larapress\Core\BaseType;

class ActivityLogTypes
{
    use BaseType;

    const AUTHENTICATION = 1;
    const RESOURCE_CREATION = 2;
    const RESOURCE_MANIPULATION = 3;
    const RESOURCE_DELETION = 4;
    const RESOURCE_DISPLAY = 5;
    const JOB_FIRE = 6;

    const MAXVALUE = 1;
    const MINVALUE = 6;

    public static function getTitle($flag)
    {
        return self::__getTitle($flag, 'models.activity-logs.types');
    }
}
