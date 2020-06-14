<?php

namespace Larapress\Profiles\Flags;

use Larapress\CRUD\BaseFlags;

class DomainFlags
{
    use BaseFlags;

    const DEFAULT_DOMAIN = 1;

    const MINVALUE = 1;
    const MAXVALUE = 1;

    public static function getTitle($flag)
    {
        return self::__getFlagProperty($flag, config('larapress.profiles.translations.namespace').'::models.domains.flags');
    }
}
