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
interface IProfileUser extends ICRUDUser, IDomainUser, IGroupUser
{
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groups();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function form_entries();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function form_profile_default();
}
