<?php

namespace Larapress\Profiles\Base;

use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\BaseFlags;
use Larapress\Profiles\Models\Group;

trait BaseGroupUser
{
    /**
     * Undocumented function
     *
     * @return Group[]
     */
    public function getMembershipGroups()
    {
        return Helpers::getCachedValue(
            'larapress.profiles.user.' . $this->id . '.groups',
            ['user.groups:' . $this->id],
            3600,
            true,
            function () {
                return $this->groups()->get();
            }
        );
    }

    /**
     * Undocumented function
     *
     * @return int[]
     */
    public function getMembershipGroupIds()
    {
        $groups = $this->getMembershipGroups();
        $ids = [];
        foreach ($groups as $group) {
            $ids[] = $group->id;
        }

        return $ids;
    }

    /**
     * Undocumented function
     *
     * @return Group[]
     */
    public function getAdministrationGroups()
    {
        $groups = $this->getMembershipGroups();
        $admins = [];

        foreach ($groups as $group) {
            if (BaseFlags::isActive($group->flags, Group::FLAGS_ADMIN)) {
                $admins[] = $group;
            }
        }

        return $admins;
    }

    /**
     * Undocumented function
     *
     * @return int[]
     */
    public function getAdministrateGroupIds()
    {
        $groups = $this->getAdministrationGroups();
        $ids = [];
        foreach ($groups as $group) {
            $ids[] = $group->id;
        }

        return $ids;
    }

    /**
     * @return void
     */
    public function forgetGroupsCache()
    {
        Helpers::forgetCachedValues(['user.groups:' . $this->id]);
    }
}
