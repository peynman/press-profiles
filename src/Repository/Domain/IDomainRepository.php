<?php

namespace Larapress\Profiles\Repository\Domain;

use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;

interface IDomainRepository
{
    /**
     * @param IProfileUser|ICRUDUser $user
     * @return Domain[]
     */
    public function getVisibleDomains(IProfileUser $user);
}
