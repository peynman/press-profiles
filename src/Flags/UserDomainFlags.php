<?php

namespace Larapress\Profiles\Flags;

use Larapress\CRUD\BaseFlags;

class UserDomainFlags
{
    use BaseFlags;

    const DEFAULT_DOMAIN = 1;
    const REGISTRATION_DOMAIN = 2;
    const MEMBERSHIP_DOMAIN = 4;
    const AFFILIATE_DOMAIN = 8;

    const MINVALUE = 1;
    const MAXVALUE = 8;

    public static function getTitle($flag)
    {
        return self::__getFlagProperty($flag, config('larapress.profiles.translations.namespace').'::models.domains.flags');
    }
}
