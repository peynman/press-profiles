<?php

namespace Larapress\Profiles;

use Larapress\Profiles\Models\Domain;

/**
 * Interface IDomainUser.
 */
interface IDomainUser {
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
}
