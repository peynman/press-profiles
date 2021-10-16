<?php

namespace Larapress\Profiles;

use Larapress\Profiles\Models\Group;

/**
 * Interface IGroupUser.
 */
interface IGroupUser {
    /**
     * Undocumented function
     *
     * @return Group[]
     */
    public function getMembershipGroups();

    /**
     * Undocumented function
     *
     * @return int[]
     */
    public function getMembershipGroupIds();

    /**
     * Undocumented function
     *
     * @return Group[]
     */
    public function getAdministrateGroups();

    /**
     * Undocumented function
     *
     * @return int[]
     */
    public function getAdministrateGroupIds();

    /**
     * @return void
     */
    public function forgetGroupsCache();
}
