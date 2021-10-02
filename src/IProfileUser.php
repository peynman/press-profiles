<?php

namespace Larapress\Profiles;

use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\PhoneNumber;
use Larapress\Profiles\Models\PhysicalAddress;
use Larapress\Profiles\Models\EmailAddress;
use Larapress\Profiles\Models\FormEntry;

/**
 * Interface IProfileUser.
 *
 * @property int                $flags
 * @property Domain[]           $domains
 * @property PhoneNumber[]      $phones
 * @property PhysicalAddress[]  $addresses
 * @property EmailAddress[]     $emails
 * @property FormEntry[]        $form_entries
 * @property FormEntry          $form_profile_default
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
     * Undocumented function
     *
     * @return int|null
     */
    public function getIntroducerId();

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses();

    /**
     * Undocumented function
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function form_profile_default();

    /**
     * Undocumented function
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function form_entries();
}
