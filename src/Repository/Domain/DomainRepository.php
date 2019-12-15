<?php

namespace Larapress\Profiles\Repository\Domain;

use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;

class DomainRepository implements IDomainRepository
{
    /**
     * @param IProfileUser|ICRUDUser $user
     * @return Domain[]
     */
    public function getVisibleDomains($user, $columns = ['id', 'title', 'data'])
    {
        $query = Domain::query();

        if ($user->hasRole(config('larapress.profiles.security.roles.master'))) {
            $query->whereIn('id', $user->getAffiliateDomainIds());
        }

        /** @var Domain[] $domains */
        $domains = $query->select($columns)->get();
        return $domains;
    }
}
