<?php


namespace Larapress\Profiles\Flags;

use Larapress\Core\BaseFlags;

class DomainFlags
{
    use BaseFlags;

    const DISABLED = 1;
    const REGISTRATION_DOMAIN = 2;
    const MEMBERSHIP_DOMAIN = 4;
    const AFFILIATE_DOMAIN = 8;

    const MINVALUE = 1;
    const MAXVALUE = 8;

    public static function getTitle($flag)
    {
        return self::__getTitle($flag, 'models.sub-domains.status');
    }
}
