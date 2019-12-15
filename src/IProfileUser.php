<?php

namespace Larapress\Profiles;

use Larapress\Profiles\Models\Domain;

/**
 * Interface IProfileUser.
 *
 * @property int        $flags
 */
interface IProfileUser
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
    public function roles();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function domains();
}
