<?php

namespace Larapress\Profiles;

use Illuminate\Support\Facades\Cache;
use Larapress\CRUD\BaseFlags;
use Larapress\CRUD\Models\Role;
use Larapress\Profiles\Flags\UserDomainFlags;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\EmailAddress;
use Larapress\Profiles\Models\PhoneNumber;

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
            if (BaseFlags::isActive($domain->flags, UserDomainFlags::REGISTRATION_DOMAIN)) {
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
            if (BaseFlags::isActive($domain->flags, UserDomainFlags::MEMBERSHIP_DOMAIN)) {
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
                    0
                );
            }
        }

        return $this->cachedDomains;
    }



    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phones()
    {
        return $this->hasMany(
            PhoneNumber::class,
            'user_id',
        );
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails()
    {
        return $this->hasMany(
            EmailAddress::class,
            'user_id',
        );
    }

    /**
     * @return void
     */
    public function forgetDomainsCache()
    {
        Cache::forget('larapress.cached.user.'.$this->id.'.domains');
    }
}
