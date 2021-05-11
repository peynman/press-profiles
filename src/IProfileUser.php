<?php

namespace Larapress\Profiles;

use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\Models\Domain;

/**
 * Interface IProfileUser.
 *
 * @property int        $flags
 */
interface IProfileUser extends ICRUDUser
{
    /**
     * @return Domain
     */
    public function getRegistrationDomain();

    /**
     * @return int
     */
    public function getRegistrationDomainId();

    /**
     * @return Domain
     */
    public function getMembershipDomain();

    /**
     * @return int
     */
    public function getMembershipDomainId();

    /**
     * @return Domain[]
     */
    public function getAffiliateDomains();

    /**
     * @return int[]
     */
    public function getAffiliateDomainIds();

    /**
     * @return void
     */
    public function forgetDomainsCache();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function domains();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phones();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails();
}
