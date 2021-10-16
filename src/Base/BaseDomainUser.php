<?php

namespace Larapress\Profiles\Base;

use Larapress\CRUD\BaseFlags;
use Larapress\CRUD\Extend\Helpers;
use Larapress\Profiles\Flags\UserDomainFlags;
use Larapress\Profiles\Models\Domain;

trait BaseDomainUser {

    /**
     * @return Domain
     */
    public function getRegistrationDomain()
    {
        $domains = $this->getDomains();
        foreach ($domains as $domain) {
            if (BaseFlags::isActive($domain->pivot->flags, UserDomainFlags::REGISTRATION_DOMAIN)) {
                return $domain;
            }
        }
    }

    /**
     * @return int
     */
    public function getRegistrationDomainId()
    {
        $domain = $this->getRegistrationDomain();

        return is_null($domain) ? null : $domain->id;
    }

    /**
     * @return Domain
     */
    public function getMembershipDomain()
    {
        $domains = $this->getDomains();
        foreach ($domains as $domain) {
            if (BaseFlags::isActive($domain->pivot->flags, UserDomainFlags::REGISTRATION_DOMAIN)) {
                return $domain;
            }
        }
    }

    /**
     * @return int
     */
    public function getMembershipDomainId()
    {
        $domain = $this->getRegistrationDomain();

        return is_null($domain) ? null : $domain->id;
    }

    /**
     * @return Domain[]
     */
    public function getAffiliateDomains()
    {
        $domains = $this->getDomains();
        $affDomains = [];

        foreach ($domains as $domain) {
            if (BaseFlags::isActive($domain->flags, UserDomainFlags::AFFILIATE_DOMAIN)) {
                $affDomains[] = $domain;
            }
        }

        return $affDomains;
    }

    /**
     * @return int[]
     */
    public function getAffiliateDomainIds()
    {
        $domains = $this->getDomains();
        $ids = [];
        foreach ($domains as $domain) {
            if (BaseFlags::isActive($domain->flags, UserDomainFlags::AFFILIATE_DOMAIN)) {
                $ids[] = $domain->id;
            }
        }

        return $ids;
    }

    /**
     * @return Domain[]
     */
    public function getDomains()
    {
        return Helpers::getCachedValue(
            'larapress.profiles.user.' . $this->id . '.domains',
            ['user.domains:' . $this->id],
            3600,
            true,
            function () {
                return $this->domains()->get();
            }
        );
    }

    /**
     * @return void
     */
    public function forgetDomainsCache()
    {
        Helpers::forgetCachedValues(['user.domains:' . $this->id]);
    }
}
