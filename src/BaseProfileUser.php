<?php

namespace Larapress\Profiles;

use Illuminate\Support\Facades\Cache;
use Larapress\Core\BaseFlags;
use Larapress\Profiles\Flags\DomainFlags;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\Role;

/**
 * Trait BaseProfileUser.
 *
 * @property int $id
 * @property string $name
 * @property string $password
 * @property Domain $domains
 * @property Role[] $roles
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 */
trait BaseProfileUser
{
    protected $cachedDomains = null;

    /**
     * @return Domain
     */
    public function getRegistrationDomain()
    {
        $domains = $this->getDomains();
        foreach ($domains as $domain) {
            if (BaseFlags::isActive($domain->flags, DomainFlags::REGISTRATION_DOMAIN)) {
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
            if (BaseFlags::isActive($domain->flags, DomainFlags::MEMBERSHIP_DOMAIN)) {
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
            if (BaseFlags::isActive($domain->flags, DomainFlags::AFFILIATE_DOMAIN)) {
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
        $domains = $this->getAffiliateDomains();
        $ids = [];
        foreach ($domains as $domain) {
            $ids[] = $domain->id;
        }

        return $ids;
    }

    /**
     * @return void
     */
    public function forgetDomainsCache()
    {
        Cache::forget('larapress.cached.user.'.$this->id.'.domains');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'user_role',
            'user_id',
            'role_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function domains()
    {
        return $this->belongsToMany(
            Domain::class,
            'user_domain',
            'user_id',
            'domain_id'
        );
    }

    /**
     * @return Domain[]
     */
    public function getDomains()
    {
        if (is_null($this->cachedDomains)) {
            $this->cachedDomains = Cache::get('larapress.cached.user.'.$this->id.'.domains', null);
            if (is_null($this->cachedDomains)) {
                $this->cachedDomains = $this->domains()->get();
                Cache::put(
                    'larapress.cached.user.'.$this->id.'.domains',
                    $this->cachedDomains,
                    \DateInterval::createFromDateString(config('larapress.profiles.defaults.cache-ttl'))
                );
            }
        }

        return $this->cachedDomains;
    }
}
