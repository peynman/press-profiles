<?php

namespace Larapress\Profiles;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Cache;
use Larapress\CRUD\BaseFlags;
use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\Models\Role;
use Larapress\Profiles\Flags\UserDomainFlags;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\EmailAddress;
use Larapress\Profiles\Models\FormEntry;
use Larapress\Profiles\Models\PhoneNumber;
use Larapress\Profiles\Models\PhysicalAddress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Trait BaseProfileUser.
 *
 * @property int $id
 * @property string $name
 * @property string $password
 * @property Collection|Domain[] $domains
 * @property Collection|Role[] $roles
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 */
trait BaseProfileUser
{
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
    public function addresses()
    {
        return $this->hasMany(
            PhysicalAddress::class,
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
     * Undocumented function
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function form_profile_default()
    {
        $roleProfileFormCases = [];
        $profileFormIds = config('larapress.profiles.form_role_profiles');
        foreach ($profileFormIds as $roleName => $formId) {
            $roleProfileFormCases[] = "WHEN `roles`.`name` = '$roleName' THEN $formId";
        }
        $roleProfileFormCases[] = "ELSE ".config('larapress.profiles.default_profile_form_id');

        $caseString = "CASE\n".implode("\n", $roleProfileFormCases)."\nEND";

        return $this->hasOne(
            FormEntry::class,
            'user_id'
        )
            ->leftJoin('user_role', function (JoinClause $join) {
                $join->on('user_role.user_id', '=', 'form_entries.user_id')
                    ->orderBy('priority', 'desc');
            })
            ->leftJoin('roles', function (JoinClause $join) {
                $join->on('roles.id', '=', 'user_role.role_id');
            })
            ->whereRaw("`form_entries`.`form_id` = ($caseString)");
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
