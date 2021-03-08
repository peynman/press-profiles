<?php

namespace Larapress\Profiles;

use Illuminate\Support\Facades\Cache;
use Larapress\CRUD\BaseFlags;
use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\Models\Role;
use Larapress\Profiles\Base\FormEntryUserSupportProfileRelationship;
use Larapress\Profiles\Flags\UserDomainFlags;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\EmailAddress;
use Larapress\Profiles\Models\FormEntry;
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
        )->withPivot('flags');
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function form_entries()
    {
        return $this->hasMany(
            FormEntry::class,
            'user_id'
        );
    }

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


    protected $cachedDomains = null;

    /**
     * @return Domain[]
     */
    public function getDomains()
    {
        if (is_null($this->cachedDomains)) {
            $this->cachedDomains = Helpers::getCachedValue(
                'larapress.cached.user.'.$this->id.'.domains',
                function () {
                    return $this->domains()->get();
                },
                ['user.domains:'.$this->id],
                null
            );
        }

        return $this->cachedDomains;
    }

    /**
     * @return void
     */
    public function forgetDomainsCache()
    {
        Cache::tags(['user.domains:'.$this->id])->flush();
    }
}
